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

class ProcessImportCarsChunkJob implements ShouldQueue
{
    use Queueable;

    private const CAR_VALIDATION_CHUNK_SIZE = 100;

    /**
     * Прогресс в import_runs пишем не на каждую машину, а не чаще чем
     * раз в столько обработанных машин или раз в столько миллисекунд.
     */
    private const PROGRESS_PERSIST_INTERVAL = 25;

    private const PROGRESS_PERSIST_INTERVAL_MS = 1000;

    /**
     * Флаг остановки проверяем не на каждой машине, а раз в столько машин,
     * лёгким запросом одного поля вместо гидрации всей модели.
     */
    private const STOP_CHECK_INTERVAL = 25;

    public int $timeout = 0;

    public function __construct(
        public ImportRun $importRun,
        public int $chunkIndex,
    ) {
    }

    public function handle(CarImportService $importService): void
    {
        $jobStartedAt = microtime(true);
        $importRun = $this->importRun->fresh();

        if ($importRun === null || in_array($importRun->status, ['succeeded', 'failed', 'cancelled'], true)) {
            return;
        }

        $totalCars = (int) ($importRun->total_cars ?? 0);
        $chunksTotal = (int) ($importRun->chunks_total ?? 0);
        $failureStage = 'chunk_bootstrap';

        try {
            if ($this->isStopRequested($importRun)) {
                $this->cleanupChunkFiles($importRun);
                $this->markCancelled(
                    $importRun,
                    "Импорт остановлен пользователем. Обработано {$importRun->processed_cars} из {$totalCars} машин.",
                    $jobStartedAt,
                );

                return;
            }

            $importRun->update([
                'status' => 'running',
                'message' => "Обрабатывается чанк {$this->chunkIndex} из {$chunksTotal}. Импортировано {$importRun->processed_cars} из {$totalCars} машин.",
                'current_stage' => 'processing_chunk',
                'current_chunk_index' => $this->chunkIndex,
                'chunks_processed' => max((int) $importRun->chunks_processed, $this->chunkIndex - 1),
                'finished_at' => null,
            ]);

            $chunkPayload = $this->readChunkFile($importRun);
            $chunkValidationStartedAt = microtime(true);
            $failureStage = 'chunk_validation';

            $this->logInfo($importRun, 'import.stage.chunk_validation_started', [
                'stage' => 'chunk_validation',
                'chunk_index' => $this->chunkIndex,
                'chunk_size' => count($chunkPayload),
                'cars_total' => $totalCars,
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            ]);

            $validatedCarsChunk = $this->validateCarsChunk(
                $importRun,
                $chunkPayload,
                ($this->chunkIndex - 1) * self::CAR_VALIDATION_CHUNK_SIZE,
                $jobStartedAt,
                $this->chunkIndex,
            );

            $this->logInfo($importRun, 'import.stage.chunk_validation_completed', [
                'stage' => 'chunk_validation',
                'chunk_index' => $this->chunkIndex,
                'chunk_size' => count($chunkPayload),
                'valid_cars_count' => count($validatedCarsChunk),
                'duration_ms' => $this->durationMs($chunkValidationStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
            ]);

            $chunkPersistStartedAt = microtime(true);
            $failureStage = 'chunk_persist';
            $initialStats = $this->statsFromImportRun($importRun);

            $progressState = [
                'cars_since_persist' => 0,
                'last_persist_at' => microtime(true),
            ];
            $stopState = [
                'cars_until_check' => 0,
                'stop_requested' => false,
            ];

            $stats = $importService->importCarsChunk(
                $validatedCarsChunk,
                $initialStats,
                function (array $updatedStats) use (&$importRun, &$progressState, $totalCars, $chunksTotal, $jobStartedAt): void {
                    $progressState['cars_since_persist']++;
                    $now = microtime(true);

                    $intervalReached = $progressState['cars_since_persist'] >= self::PROGRESS_PERSIST_INTERVAL
                        || ($now - $progressState['last_persist_at']) * 1000 >= self::PROGRESS_PERSIST_INTERVAL_MS;

                    if (! $intervalReached) {
                        return;
                    }

                    $progressState['cars_since_persist'] = 0;
                    $progressState['last_persist_at'] = $now;

                    $this->persistProgress(
                        $importRun,
                        $updatedStats,
                        $totalCars,
                        $chunksTotal,
                        $this->chunkIndex,
                        $this->chunkIndex - 1,
                        false,
                        "Обрабатывается чанк {$this->chunkIndex} из {$chunksTotal}. Импортировано {$updatedStats['processed_cars']} из {$totalCars} машин.",
                        $jobStartedAt,
                    );
                },
                function () use (&$stopState): bool {
                    if ($stopState['stop_requested']) {
                        return true;
                    }

                    if ($stopState['cars_until_check'] > 0) {
                        $stopState['cars_until_check']--;

                        return false;
                    }

                    $stopState['cars_until_check'] = self::STOP_CHECK_INTERVAL - 1;
                    $stopRequestedAt = ImportRun::query()
                        ->whereKey($this->importRun->id)
                        ->value('stop_requested_at');
                    $stopState['stop_requested'] = $stopRequestedAt !== null;

                    return $stopState['stop_requested'];
                },
            );

            $chunkProcessedCars = $stats['processed_cars'] - $initialStats['processed_cars'];
            $chunkCompleted = $chunkProcessedCars === count($validatedCarsChunk);
            $chunksProcessed = $chunkCompleted
                ? $this->chunkIndex
                : max((int) $importRun->chunks_processed, $this->chunkIndex - 1);

            $this->persistProgress(
                $importRun,
                $stats,
                $totalCars,
                $chunksTotal,
                $this->chunkIndex,
                $chunksProcessed,
                true,
                $chunkCompleted
                    ? "Чанк {$this->chunkIndex} из {$chunksTotal} завершен. Импортировано {$stats['processed_cars']} из {$totalCars} машин."
                    : "Обработка чанка {$this->chunkIndex} остановлена. Импортировано {$stats['processed_cars']} из {$totalCars} машин.",
                $jobStartedAt,
            );

            $this->logInfo($importRun, 'import.stage.chunk_persist_completed', [
                'stage' => 'chunk_persist',
                'chunk_index' => $this->chunkIndex,
                'chunk_size' => count($validatedCarsChunk),
                'duration_ms' => $this->durationMs($chunkPersistStartedAt),
                'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                'processed_cars' => $stats['processed_cars'],
                'total_cars' => $totalCars,
                'stats' => $this->compactStats($stats),
            ]);

            $importRun = $importRun->fresh() ?? $importRun;

            if ($this->isStopRequested($importRun) || ! $chunkCompleted) {
                $this->cleanupChunkFiles($importRun);
                $this->markCancelled(
                    $importRun,
                    "Импорт остановлен пользователем. Обработано {$stats['processed_cars']} из {$totalCars} машин.",
                    $jobStartedAt,
                    [
                        'processed_cars' => $stats['processed_cars'],
                        'chunks_processed' => $chunksProcessed,
                        'current_chunk_index' => $this->chunkIndex,
                        'stats_new' => $stats['new'],
                        'stats_updated' => $stats['updated'],
                        'stats_unchanged' => $stats['unchanged'],
                        'stats_processed' => $stats['processed'],
                    ],
                );

                return;
            }

            if ($this->chunkIndex >= $chunksTotal) {
                $this->cleanupChunkFiles($importRun);
                $importRun->update([
                    'status' => 'succeeded',
                    'message' => 'Импорт завершен успешно.',
                    'current_stage' => 'completed',
                    'processed_cars' => $stats['processed_cars'],
                    'chunks_processed' => $chunksProcessed,
                    'current_chunk_index' => null,
                    'stats_new' => $stats['new'],
                    'stats_updated' => $stats['updated'],
                    'stats_unchanged' => $stats['unchanged'],
                    'stats_processed' => $stats['processed'],
                    'finished_at' => now(),
                ]);

                $this->logInfo($importRun, 'import.stage.cars_completed', [
                    'stage' => 'persist_cars',
                    'elapsed_ms' => $this->elapsedMs($jobStartedAt),
                    'processed_cars' => $stats['processed_cars'],
                    'total_cars' => $totalCars,
                    'stats' => $this->compactStats($stats),
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

            $nextChunkIndex = $this->chunkIndex + 1;
            $importRun->update([
                'message' => "Чанк {$this->chunkIndex} из {$chunksTotal} завершен. Ожидает запуска чанк {$nextChunkIndex}.",
                'current_stage' => 'queued_chunks',
                'chunks_processed' => $chunksProcessed,
                'current_chunk_index' => $nextChunkIndex,
            ]);

            $this->dispatchNextChunk($importRun, $nextChunkIndex, $importService);
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
                'chunk_index' => $this->chunkIndex,
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

        $this->cleanupChunkFiles($importRun);

        $importRun->update([
            'status' => 'failed',
            'message' => 'Импорт завершился с ошибкой.',
            'current_stage' => 'failed',
            'error_message' => $exception->getMessage(),
            'finished_at' => now(),
        ]);

        $this->logError($importRun, 'import.failed', [
            'stage' => 'queue_failed',
            'chunk_index' => $this->chunkIndex,
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readChunkFile(ImportRun $importRun): array
    {
        $path = $this->chunkFilePath($importRun, $this->chunkIndex);

        if (!Storage::disk('local')->exists($path)) {
            throw new RuntimeException("Файл чанка #{$this->chunkIndex} не найден.");
        }

        $decoded = json_decode(
            Storage::disk('local')->get($path),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        if (!is_array($decoded)) {
            throw new RuntimeException("Чанк #{$this->chunkIndex} имеет некорректный формат.");
        }

        return $decoded;
    }

    private function chunksDirectory(ImportRun $importRun): string
    {
        return "imports/chunks/{$importRun->id}";
    }

    private function chunkFilePath(ImportRun $importRun, int $chunkIndex): string
    {
        return sprintf('%s/chunk-%05d.json', $this->chunksDirectory($importRun), $chunkIndex);
    }

    private function cleanupChunkFiles(ImportRun $importRun): void
    {
        Storage::disk('local')->deleteDirectory($this->chunksDirectory($importRun));
    }

    private function dispatchNextChunk(ImportRun $importRun, int $chunkIndex, CarImportService $importService): void
    {
        $job = new self($importRun, $chunkIndex);

        if (config('queue.default') === 'sync') {
            $job->handle($importService);

            return;
        }

        dispatch($job);
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
     * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
     */
    private function statsFromImportRun(ImportRun $importRun): array
    {
        return [
            'new' => (int) $importRun->stats_new,
            'updated' => (int) $importRun->stats_updated,
            'unchanged' => (int) $importRun->stats_unchanged,
            'processed' => (int) $importRun->stats_processed,
            'processed_cars' => (int) $importRun->processed_cars,
        ];
    }

    /**
     * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
     */
    private function persistProgress(
        ImportRun $importRun,
        array $stats,
        int $totalCars,
        int $chunksTotal,
        int $currentChunkIndex,
        int $chunksProcessed,
        bool $writeLog,
        string $message,
        float $jobStartedAt,
    ): void {
        $importRun->forceFill([
            'message' => $message,
            'current_stage' => 'processing_chunk',
            'processed_cars' => $stats['processed_cars'],
            'chunks_total' => $chunksTotal,
            'chunks_processed' => $chunksProcessed,
            'current_chunk_index' => $currentChunkIndex,
            'stats_new' => $stats['new'],
            'stats_updated' => $stats['updated'],
            'stats_unchanged' => $stats['unchanged'],
            'stats_processed' => $stats['processed'],
        ])->save();

        if (! $writeLog) {
            return;
        }

        $this->logInfo($importRun, 'import.progress', [
            'stage' => 'persist_cars',
            'chunk_index' => $currentChunkIndex,
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
            'chunk_index' => $this->chunkIndex,
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
