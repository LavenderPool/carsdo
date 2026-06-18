<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Services\Import\CarImportService;
use App\Support\Import\ImportPayloadRules;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Throwable;

class ProcessImportJsonJob implements ShouldQueue
{
    use Queueable;

    private const CAR_VALIDATION_CHUNK_SIZE = 100;

    public int $timeout = 0;

    public function __construct(
        public ImportRun $importRun,
    ) {
    }

    public function handle(CarImportService $importService): void
    {
        $jobStartedAt = microtime(true);
        $importRun = $this->importRun->fresh();

        if ($importRun === null) {
            return;
        }

        if ($this->isStopRequested($importRun)) {
            $this->markCancelled($importRun, 'Импорт остановлен до начала обработки.', $jobStartedAt);

            return;
        }

        $importRun->update([
            'status' => 'running',
            'message' => 'Чтение JSON-файла...',
            'error_message' => null,
            'started_at' => now(),
            'finished_at' => null,
        ]);

        $this->logInfo($importRun, 'import.started', [
            'stage' => 'job_started',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
        ]);

        $failureStage = 'parse_payload';

        try {
            $payload = $this->parsePayload($importRun, $jobStartedAt);
            $totalCars = count($payload['cars']);

            if ($this->isStopRequested($importRun)) {
                $this->markCancelled($importRun, 'Импорт остановлен после чтения входных данных.', $jobStartedAt);

                return;
            }

            $importRun->update([
                'message' => $totalCars > 0
                    ? "Импортировано 0 из {$totalCars} машин."
                    : 'Файл прочитан, машин для импорта нет.',
                'total_cars' => $totalCars,
                'processed_cars' => 0,
                'stats_new' => 0,
                'stats_updated' => 0,
                'stats_unchanged' => 0,
                'stats_processed' => 0,
            ]);

            $this->logInfo($importRun, 'import.stage.persist_started', [
                'stage' => 'persist_started',
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'total_cars' => $totalCars,
            ]);

            $failureStage = 'persist_data';

            $citiesStageStartedAt = microtime(true);
            $stats = $importService->importCities($payload['cities']);

            $this->logInfo($importRun, 'import.stage.cities_completed', [
                'stage' => 'persist_cities',
                'duration_ms' => $this->durationMs($citiesStageStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'stats' => $this->compactStats($stats),
            ]);

            $brandsStageStartedAt = microtime(true);
            $stats = $importService->importBrands($payload['brands'], $stats);

            $this->logInfo($importRun, 'import.stage.brands_completed', [
                'stage' => 'persist_brands',
                'duration_ms' => $this->durationMs($brandsStageStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'stats' => $this->compactStats($stats),
            ]);

            $carsChunks = array_chunk($payload['cars'], self::CAR_VALIDATION_CHUNK_SIZE);

            foreach ($carsChunks as $chunkZeroBasedIndex => $carsChunk) {
                if ($this->isStopRequested($importRun)) {
                    $this->markCancelled(
                        $importRun,
                        "Импорт остановлен пользователем. Обработано {$stats['processed_cars']} из {$totalCars} машин.",
                        $jobStartedAt,
                        [
                            'processed_cars' => $stats['processed_cars'],
                            'total_cars' => $totalCars,
                            'stats_new' => $stats['new'],
                            'stats_updated' => $stats['updated'],
                            'stats_unchanged' => $stats['unchanged'],
                            'stats_processed' => $stats['processed'],
                        ],
                    );

                    return;
                }

                $chunkIndex = $chunkZeroBasedIndex + 1;
                $chunkValidationStartedAt = microtime(true);

                $this->logInfo($importRun, 'import.stage.chunk_validation_started', [
                    'stage' => 'chunk_validation',
                    'chunk_index' => $chunkIndex,
                    'chunk_size' => count($carsChunk),
                    'cars_total' => $totalCars,
                    'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                ]);

                $validatedCarsChunk = $this->validateCarsChunk(
                    $importRun,
                    $carsChunk,
                    $chunkZeroBasedIndex * self::CAR_VALIDATION_CHUNK_SIZE,
                    $jobStartedAt,
                    $chunkIndex,
                );

                $this->logInfo($importRun, 'import.stage.chunk_validation_completed', [
                    'stage' => 'chunk_validation',
                    'chunk_index' => $chunkIndex,
                    'chunk_size' => count($carsChunk),
                    'valid_cars_count' => count($validatedCarsChunk),
                    'duration_ms' => $this->durationMs($chunkValidationStartedAt),
                    'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                ]);

                $chunkPersistStartedAt = microtime(true);

                $stats = $importService->importCarsChunk(
                    $validatedCarsChunk,
                    $stats,
                    null,
                    function () use (&$importRun): bool {
                        $freshImportRun = $importRun->fresh();

                        if ($freshImportRun === null) {
                            return true;
                        }

                        $importRun = $freshImportRun;

                        return $this->isStopRequested($freshImportRun);
                    },
                );

                $this->logInfo($importRun, 'import.stage.chunk_persist_completed', [
                    'stage' => 'chunk_persist',
                    'chunk_index' => $chunkIndex,
                    'chunk_size' => count($validatedCarsChunk),
                    'duration_ms' => $this->durationMs($chunkPersistStartedAt),
                    'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                    'processed_cars' => $stats['processed_cars'],
                    'total_cars' => $totalCars,
                    'stats' => $this->compactStats($stats),
                ]);

                $this->persistProgress($importRun, $stats, $totalCars, $jobStartedAt);
            }

            $this->logInfo($importRun, 'import.stage.cars_completed', [
                'stage' => 'persist_cars',
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'processed_cars' => $stats['processed_cars'],
                'total_cars' => $totalCars,
                'stats' => $this->compactStats($stats),
            ]);

            if ($this->isStopRequested($importRun)) {
                $this->markCancelled(
                    $importRun,
                    "Импорт остановлен пользователем. Обработано {$stats['processed_cars']} из {$totalCars} машин.",
                    $jobStartedAt,
                    [
                        'processed_cars' => $stats['processed_cars'],
                        'total_cars' => $totalCars,
                        'stats_new' => $stats['new'],
                        'stats_updated' => $stats['updated'],
                        'stats_unchanged' => $stats['unchanged'],
                        'stats_processed' => $stats['processed'],
                    ],
                );

                return;
            }

            $importRun->update([
                'status' => 'succeeded',
                'message' => 'Импорт завершен успешно.',
                'processed_cars' => $stats['processed_cars'],
                'stats_new' => $stats['new'],
                'stats_updated' => $stats['updated'],
                'stats_unchanged' => $stats['unchanged'],
                'stats_processed' => $stats['processed'],
                'finished_at' => now(),
            ]);

            $this->logInfo($importRun, 'import.succeeded', [
                'stage' => 'finished',
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'processed_cars' => $stats['processed_cars'],
                'total_cars' => $totalCars,
                'stats' => $this->compactStats($stats),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            $importRun->update([
                'status' => 'failed',
                'message' => 'Импорт завершился с ошибкой.',
                'error_message' => $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'Во время импорта произошла непредвиденная ошибка.',
                'finished_at' => now(),
            ]);

            $this->logError($importRun, 'import.failed', [
                'stage' => $failureStage,
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
            ]);
        }
    }

    public function failed(Throwable $exception): void
    {
        $importRun = $this->importRun->fresh();

        if ($importRun === null || in_array($importRun->status, ['succeeded', 'failed', 'cancelled'], true)) {
            return;
        }

        $importRun->update([
            'status' => 'failed',
            'message' => 'Импорт завершился с ошибкой.',
            'error_message' => $exception->getMessage(),
            'finished_at' => now(),
        ]);

        $this->logError($importRun, 'import.failed', [
            'stage' => 'queue_failed',
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
        ]);
    }

    /**
     * @return array{cities: array<int, array<string, mixed>>, brands: array<int, array<string, mixed>>, cars: array<int, array<string, mixed>>}
     */
    private function parsePayload(ImportRun $importRun, float $jobStartedAt): array
    {
        $readStageStartedAt = microtime(true);
        $rawJson = Storage::disk('local')->get($importRun->file_path);

        $this->logInfo($importRun, 'import.stage.file_read', [
            'stage' => 'file_read',
            'duration_ms' => $this->durationMs($readStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'bytes' => strlen($rawJson),
            'file_size' => $importRun->file_size,
        ]);

        $decodeStageStartedAt = microtime(true);

        try {
            $payload = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new RuntimeException('Файл не является валидным JSON.', previous: $exception);
        }

        $this->logInfo($importRun, 'import.stage.json_decoded', [
            'stage' => 'json_decoded',
            'duration_ms' => $this->durationMs($decodeStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
        ]);

        if (!is_array($payload)) {
            throw new RuntimeException('Структура JSON не соответствует ожидаемому payload.');
        }

        $cities = $payload['cities'] ?? [];
        $brands = $payload['brands'] ?? [];
        $cars = $payload['cars'] ?? null;

        if (!is_array($cities) || !is_array($brands) || !is_array($cars)) {
            throw new RuntimeException('Структура JSON не соответствует ожидаемому payload.');
        }

        $this->logInfo($importRun, 'import.stage.structure_ok', [
            'stage' => 'structure_check',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cities_count' => count($cities),
            'brands_count' => count($brands),
            'cars_count' => count($cars),
        ]);

        $validationStageStartedAt = microtime(true);

        $validator = Validator::make([
            'cities' => $cities,
            'brands' => $brands,
            'cars' => $cars,
        ], ImportPayloadRules::rootRules(), ImportPayloadRules::messages());

        if ($validator->fails()) {
            $this->logWarning($importRun, 'import.stage.root_validation_failed', [
                'stage' => 'root_validation',
                'duration_ms' => $this->durationMs($validationStageStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'error_count' => $validator->errors()->count(),
                'first_error' => (string) $validator->errors()->first(),
            ]);

            throw new RuntimeException((string) $validator->errors()->first());
        }

        /** @var array{cities?: array<int, array<string, mixed>>, brands?: array<int, array<string, mixed>>, cars: array<int, array<string, mixed>>} $validated */
        $validated = $validator->validated();
        $validatedCities = $this->validateCities($validated['cities'] ?? []);
        $validatedBrands = $this->validateBrands($validated['brands'] ?? []);

        $this->logInfo($importRun, 'import.stage.root_validation_ok', [
            'stage' => 'root_validation',
            'duration_ms' => $this->durationMs($validationStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cities_count' => count($validatedCities),
            'brands_count' => count($validatedBrands),
            'cars_count' => count($validated['cars']),
        ]);

        $this->logInfo($importRun, 'import.stage.validation_ok', [
            'stage' => 'validation',
            'duration_ms' => $this->durationMs($validationStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cities_count' => count($validatedCities),
            'brands_count' => count($validatedBrands),
            'cars_count' => count($validated['cars']),
        ]);

        return [
            'cities' => $validatedCities,
            'brands' => $validatedBrands,
            'cars' => $validated['cars'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $cities
     * @return array<int, array<string, mixed>>
     */
    private function validateCities(array $cities): array
    {
        $validatedCities = [];

        foreach ($cities as $cityIndex => $cityPayload) {
            $validator = Validator::make(
                $cityPayload,
                ImportPayloadRules::cityRules(),
                ImportPayloadRules::messages(),
            );

            if ($validator->fails()) {
                $humanIndex = $cityIndex + 1;
                throw new RuntimeException("Ошибка валидации города #{$humanIndex}: {$validator->errors()->first()}");
            }

            /** @var array<string, mixed> $validatedCity */
            $validatedCity = $validator->validated();
            $validatedCities[] = $validatedCity;
        }

        return $validatedCities;
    }

    /**
     * @param  array<int, array<string, mixed>>  $brands
     * @return array<int, array<string, mixed>>
     */
    private function validateBrands(array $brands): array
    {
        $validatedBrands = [];

        foreach ($brands as $brandIndex => $brandPayload) {
            $validator = Validator::make(
                $brandPayload,
                ImportPayloadRules::brandRules(),
                ImportPayloadRules::messages(),
            );

            if ($validator->fails()) {
                $humanIndex = $brandIndex + 1;
                throw new RuntimeException("Ошибка валидации бренда #{$humanIndex}: {$validator->errors()->first()}");
            }

            /** @var array<string, mixed> $validatedBrand */
            $validatedBrand = $validator->validated();
            $validatedBrands[] = $validatedBrand;
        }

        return $validatedBrands;
    }

    /**
     * @param  array<int, array<string, mixed>>  $carsChunk
     * @return array<int, array<string, mixed>>
     */
    private function validateCarsChunk(
        ImportRun $importRun,
        array $carsChunk,
        int $chunkOffset,
        float $jobStartedAt,
        int $chunkIndex,
    ): array {
        $validatedCars = [];

        foreach ($carsChunk as $localIndex => $carPayload) {
            $validator = Validator::make(
                $carPayload,
                ImportPayloadRules::carRules(),
                ImportPayloadRules::messages(),
            );

            if ($validator->fails()) {
                $globalCarIndex = $chunkOffset + $localIndex + 1;
                $this->logWarning($importRun, 'import.stage.chunk_validation_failed', [
                    'stage' => 'chunk_validation',
                    'chunk_index' => $chunkIndex,
                    'car_index' => $globalCarIndex,
                    'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                    'first_error' => (string) $validator->errors()->first(),
                ]);

                throw new RuntimeException("Ошибка валидации машины #{$globalCarIndex}: {$validator->errors()->first()}");
            }

            /** @var array<string, mixed> $validatedCar */
            $validatedCar = $validator->validated();
            $validatedCars[] = $validatedCar;
        }

        return $validatedCars;
    }

    /**
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function persistProgress(ImportRun $importRun, array $stats, int $totalCars, float $jobStartedAt): void
    {
        $importRun->forceFill([
            'message' => "Импортировано {$stats['processed_cars']} из {$totalCars} машин.",
            'processed_cars' => $stats['processed_cars'],
            'stats_new' => $stats['new'],
            'stats_updated' => $stats['updated'],
            'stats_unchanged' => $stats['unchanged'],
            'stats_processed' => $stats['processed'],
        ])->save();

        $this->logInfo($importRun, 'import.progress', [
            'stage' => 'persist_cars',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'processed_cars' => $stats['processed_cars'],
            'total_cars' => $totalCars,
            'percent' => $totalCars > 0
                ? (int) round(($stats['processed_cars'] / $totalCars) * 100)
                : 0,
            'stats' => $this->compactStats($stats),
        ]);
    }

    private function isStopRequested(ImportRun $importRun): bool
    {
        return $importRun->stop_requested_at !== null;
    }

    /**
     * @param  array<string, mixed>  $extraFields
     */
    private function markCancelled(
        ImportRun $importRun,
        string $message,
        float $jobStartedAt,
        array $extraFields = [],
    ): void {
        $importRun->update([
            'status' => 'cancelled',
            'message' => $message,
            'finished_at' => now(),
            ...$extraFields,
        ]);

        $this->logInfo($importRun, 'import.cancelled', [
            'stage' => 'cancelled',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            ...$extraFields,
        ]);
    }

    /**
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     * @return array{new: int, updated: int, unchanged: int, processed: int}
     */
    private function compactStats(array $stats): array
    {
        return [
            'new' => $stats['new'],
            'updated' => $stats['updated'],
            'unchanged' => $stats['unchanged'],
            'processed' => $stats['processed'],
        ];
    }

    private function durationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }

    private function elapsedMs(float $jobStartedAt): int
    {
        return $this->durationMs($jobStartedAt);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logInfo(ImportRun $importRun, string $event, array $context = []): void
    {
        Log::info($event, $this->logContext($importRun, $context));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logWarning(ImportRun $importRun, string $event, array $context = []): void
    {
        Log::warning($event, $this->logContext($importRun, $context));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logError(ImportRun $importRun, string $event, array $context = []): void
    {
        Log::error($event, $this->logContext($importRun, $context));
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function logContext(ImportRun $importRun, array $context = []): array
    {
        return [
            'import_run_id' => $importRun->id,
            'correlation_id' => (string) $importRun->id,
            'user_id' => $importRun->user_id,
            'status' => $importRun->status,
            ...$context,
        ];
    }
}
