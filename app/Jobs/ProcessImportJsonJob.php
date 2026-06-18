<?php

namespace App\Jobs;

use App\Jobs\ProcessImportCarsChunkJob;
use App\Models\ImportRun;
use App\Services\Import\CarImportService;
use App\Support\Import\ImportPayloadRules;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JsonMachine\Exception\PathNotFoundException;
use JsonMachine\Exception\SyntaxErrorException;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
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
            'current_stage' => 'reading_file',
            'error_message' => null,
            'started_at' => now(),
            'finished_at' => null,
            'chunks_total' => 0,
            'chunks_processed' => 0,
            'current_chunk_index' => null,
        ]);

        $this->logInfo($importRun, 'import.started', [
            'stage' => 'job_started',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
        ]);

        $failureStage = 'parse_payload';

        try {
            $payload = $this->parsePayload($importRun, $jobStartedAt);
            $totalCars = $payload['total_cars'];
            $chunksTotal = $payload['chunks_total'];
            $importRun = $importRun->fresh() ?? $importRun;

            if ($this->isStopRequested($importRun)) {
                $this->cleanupChunkFiles($importRun);
                $this->markCancelled($importRun, 'Импорт остановлен после чтения входных данных.', $jobStartedAt);

                return;
            }

            $importRun->update([
                'message' => $totalCars > 0
                    ? "Подготовлено {$chunksTotal} чанков. Импортировано 0 из {$totalCars} машин."
                    : 'Файл прочитан, машин для импорта нет.',
                'current_stage' => 'persisting_references',
                'total_cars' => $totalCars,
                'processed_cars' => 0,
                'chunks_total' => $chunksTotal,
                'chunks_processed' => 0,
                'current_chunk_index' => null,
                'stats_new' => 0,
                'stats_updated' => 0,
                'stats_unchanged' => 0,
                'stats_processed' => 0,
            ]);

            $this->logInfo($importRun, 'import.stage.persist_started', [
                'stage' => 'persist_started',
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'total_cars' => $totalCars,
                'chunks_total' => $chunksTotal,
            ]);

            $failureStage = 'persist_data';

            $brandsStageStartedAt = microtime(true);
            $stats = $importService->importBrands($payload['brands']);

            $this->logInfo($importRun, 'import.stage.brands_completed', [
                'stage' => 'persist_brands',
                'duration_ms' => $this->durationMs($brandsStageStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'stats' => $this->compactStats($stats),
            ]);

            $citiesStageStartedAt = microtime(true);
            $stats = $importService->importCities($payload['cities'], $stats);

            $this->logInfo($importRun, 'import.stage.cities_completed', [
                'stage' => 'persist_cities',
                'duration_ms' => $this->durationMs($citiesStageStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'stats' => $this->compactStats($stats),
            ]);

            $importRun->update([
                'stats_new' => $stats['new'],
                'stats_updated' => $stats['updated'],
                'stats_unchanged' => $stats['unchanged'],
                'stats_processed' => $stats['processed'],
            ]);

            $importRun = $importRun->fresh() ?? $importRun;

            if ($this->isStopRequested($importRun)) {
                $this->cleanupChunkFiles($importRun);
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

            if ($chunksTotal === 0) {
                $this->cleanupChunkFiles($importRun);
                $importRun->update([
                    'status' => 'succeeded',
                    'message' => 'Импорт завершен успешно.',
                    'current_stage' => 'completed',
                    'processed_cars' => $stats['processed_cars'],
                    'chunks_processed' => 0,
                    'current_chunk_index' => null,
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

                return;
            }

            $importRun->update([
                'message' => "Подготовка завершена. Запускаем обработку чанка 1 из {$chunksTotal}.",
                'current_stage' => 'queued_chunks',
            ]);

            $this->dispatchNextChunk($importRun, 1, $importService);
        } catch (Throwable $exception) {
            report($exception);

            $this->cleanupChunkFiles($importRun);
            $importRun->update([
                'status' => 'failed',
                'message' => 'Импорт завершился с ошибкой.',
                'current_stage' => 'failed',
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
            'current_stage' => 'failed',
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
     * @return array{
     *     cities: array<int, array<string, mixed>>,
     *     brands: array<int, array<string, mixed>>,
     *     total_cars: int,
     *     chunks_total: int
     * }
     */
    private function parsePayload(ImportRun $importRun, float $jobStartedAt): array
    {
        $readStageStartedAt = microtime(true);
        $filePath = $this->resolveImportFilePath($importRun);

        $this->logInfo($importRun, 'import.stage.file_read', [
            'stage' => 'file_read',
            'duration_ms' => $this->durationMs($readStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'bytes' => $importRun->file_size,
            'file_size' => $importRun->file_size,
            'mode' => 'streaming',
        ]);

        $validationStageStartedAt = microtime(true);

        try {
            $validatedCities = $this->readValidatedCollection(
                $filePath,
                '/cities',
                fn (array $cities): array => $this->validateCities($cities),
            );
            $validatedBrands = $this->readValidatedCollection(
                $filePath,
                '/brands',
                fn (array $brands): array => $this->validateBrands($brands),
            );
            $chunkManifest = $this->splitCarsIntoChunkFiles($importRun, $filePath, $jobStartedAt);
            $totalCars = $chunkManifest['total_cars'];
            $chunksTotal = $chunkManifest['chunks_total'];
        } catch (RuntimeException $exception) {
            $this->logWarning($importRun, 'import.stage.root_validation_failed', [
                'stage' => 'root_validation',
                'duration_ms' => $this->durationMs($validationStageStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'error_count' => 1,
                'first_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $this->logInfo($importRun, 'import.stage.structure_ok', [
            'stage' => 'structure_check',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cities_count' => count($validatedCities),
            'brands_count' => count($validatedBrands),
            'cars_count' => $totalCars,
            'chunks_total' => $chunksTotal,
        ]);

        $this->logInfo($importRun, 'import.stage.root_validation_ok', [
            'stage' => 'root_validation',
            'duration_ms' => $this->durationMs($validationStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cities_count' => count($validatedCities),
            'brands_count' => count($validatedBrands),
            'cars_count' => $totalCars,
            'chunks_total' => $chunksTotal,
        ]);

        $this->logInfo($importRun, 'import.stage.validation_ok', [
            'stage' => 'validation',
            'duration_ms' => $this->durationMs($validationStageStartedAt),
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cities_count' => count($validatedCities),
            'brands_count' => count($validatedBrands),
            'cars_count' => $totalCars,
            'chunks_total' => $chunksTotal,
        ]);

        return [
            'cities' => $validatedCities,
            'brands' => $validatedBrands,
            'total_cars' => $totalCars,
            'chunks_total' => $chunksTotal,
        ];
    }

    /**
     * @return array{total_cars: int, chunks_total: int}
     */
    private function splitCarsIntoChunkFiles(ImportRun $importRun, string $filePath, float $jobStartedAt): array
    {
        $disk = Storage::disk('local');
        $disk->deleteDirectory($this->chunksDirectory($importRun));
        $disk->makeDirectory($this->chunksDirectory($importRun));

        $carsChunk = [];
        $carsCount = 0;
        $chunksTotal = 0;

        foreach ($this->iterateCars($filePath) as $carPayload) {
            $carsChunk[] = $carPayload;
            $carsCount++;

            if (count($carsChunk) < self::CAR_VALIDATION_CHUNK_SIZE) {
                continue;
            }

            $chunksTotal++;
            $this->writeChunkFile($importRun, $chunksTotal, $carsChunk);
            $carsChunk = [];
        }

        if ($carsChunk !== []) {
            $chunksTotal++;
            $this->writeChunkFile($importRun, $chunksTotal, $carsChunk);
        }

        $this->logInfo($importRun, 'import.stage.chunks_prepared', [
            'stage' => 'prepare_chunks',
            'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            'cars_count' => $carsCount,
            'chunks_total' => $chunksTotal,
            'chunk_size' => self::CAR_VALIDATION_CHUNK_SIZE,
        ]);

        return [
            'total_cars' => $carsCount,
            'chunks_total' => $chunksTotal,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $carsChunk
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    private function processCarsChunk(
        ImportRun &$importRun,
        CarImportService $importService,
        array $carsChunk,
        int $chunkOffset,
        int $chunkZeroBasedIndex,
        array $stats,
        int $totalCars,
        float $jobStartedAt,
    ): array {
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
            $chunkOffset,
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

        return $stats;
    }

    /**
     * @param  callable(array<int, array<string, mixed>>): array<int, array<string, mixed>>  $validator
     * @return array<int, array<string, mixed>>
     */
    private function readValidatedCollection(string $filePath, string $pointer, callable $validator): array
    {
        $items = [];

        try {
            foreach (Items::fromFile($filePath, [
                'pointer' => $pointer,
                'decoder' => $this->streamDecoder(),
            ]) as $itemKey => $itemPayload) {
                if (!is_int($itemKey) || !is_array($itemPayload)) {
                    throw new RuntimeException('Структура JSON не соответствует ожидаемому payload.');
                }

                $items[] = $itemPayload;
            }
        } catch (PathNotFoundException) {
            return [];
        } catch (SyntaxErrorException $exception) {
            throw new RuntimeException('Файл не является валидным JSON.', previous: $exception);
        }

        return $validator($items);
    }

    private function countCars(string $filePath): int
    {
        $carsCount = 0;

        try {
            foreach (Items::fromFile($filePath, [
                'pointer' => '/cars',
                'decoder' => $this->streamDecoder(),
            ]) as $itemKey => $itemPayload) {
                if (!is_int($itemKey)) {
                    throw new RuntimeException('Структура JSON не соответствует ожидаемому payload.');
                }

                if (!is_array($itemPayload)) {
                    $humanIndex = $carsCount + 1;
                    throw new RuntimeException("Ошибка валидации машины #{$humanIndex}: Структура машины должна быть объектом JSON.");
                }

                $carsCount++;
            }
        } catch (PathNotFoundException $exception) {
            throw new RuntimeException(ImportPayloadRules::messages()['cars.required'], previous: $exception);
        } catch (SyntaxErrorException $exception) {
            throw new RuntimeException('Файл не является валидным JSON.', previous: $exception);
        }

        return $carsCount;
    }

    /**
     * @return \Generator<int, array<string, mixed>>
     */
    private function iterateCars(string $filePath): \Generator
    {
        try {
            foreach (Items::fromFile($filePath, [
                'pointer' => '/cars',
                'decoder' => $this->streamDecoder(),
            ]) as $itemKey => $itemPayload) {
                if (!is_int($itemKey)) {
                    throw new RuntimeException('Структура JSON не соответствует ожидаемому payload.');
                }

                if (!is_array($itemPayload)) {
                    $humanIndex = $itemKey + 1;
                    throw new RuntimeException("Ошибка валидации машины #{$humanIndex}: Структура машины должна быть объектом JSON.");
                }

                yield $itemPayload;
            }
        } catch (PathNotFoundException $exception) {
            throw new RuntimeException(ImportPayloadRules::messages()['cars.required'], previous: $exception);
        } catch (SyntaxErrorException $exception) {
            throw new RuntimeException('Файл не является валидным JSON.', previous: $exception);
        }
    }

    private function resolveImportFilePath(ImportRun $importRun): string
    {
        if (!Storage::disk('local')->exists($importRun->file_path)) {
            throw new RuntimeException('Файл импорта не найден.');
        }

        return Storage::disk('local')->path($importRun->file_path);
    }

    private function chunksDirectory(ImportRun $importRun): string
    {
        return "imports/chunks/{$importRun->id}";
    }

    private function chunkFilePath(ImportRun $importRun, int $chunkIndex): string
    {
        return sprintf('%s/chunk-%05d.json', $this->chunksDirectory($importRun), $chunkIndex);
    }

    /**
     * @param  array<int, array<string, mixed>>  $carsChunk
     */
    private function writeChunkFile(ImportRun $importRun, int $chunkIndex, array $carsChunk): void
    {
        Storage::disk('local')->put(
            $this->chunkFilePath($importRun, $chunkIndex),
            json_encode($carsChunk, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );
    }

    private function cleanupChunkFiles(ImportRun $importRun): void
    {
        Storage::disk('local')->deleteDirectory($this->chunksDirectory($importRun));
    }

    private function dispatchNextChunk(ImportRun $importRun, int $chunkIndex, CarImportService $importService): void
    {
        $job = new ProcessImportCarsChunkJob($importRun, $chunkIndex);

        if (config('queue.default') === 'sync') {
            $job->handle($importService);

            return;
        }

        dispatch($job);
    }

    private function streamDecoder(): ExtJsonDecoder
    {
        return new ExtJsonDecoder(true);
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
            if (!is_array($carPayload)) {
                $globalCarIndex = $chunkOffset + $localIndex + 1;
                $this->logWarning($importRun, 'import.stage.chunk_validation_failed', [
                    'stage' => 'chunk_validation',
                    'chunk_index' => $chunkIndex,
                    'car_index' => $globalCarIndex,
                    'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                    'first_error' => 'Структура машины должна быть объектом JSON.',
                ]);

                throw new RuntimeException("Ошибка валидации машины #{$globalCarIndex}: Структура машины должна быть объектом JSON.");
            }

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
            'current_stage' => 'cancelled',
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
