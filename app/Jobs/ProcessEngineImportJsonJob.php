<?php

namespace App\Jobs;

use App\Models\EngineImportRun;
use App\Services\Import\EngineImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProcessEngineImportJsonJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 0;

    public function __construct(
        public EngineImportRun $engineImportRun,
    ) {
    }

    public function handle(EngineImportService $importService): void
    {
        $run = $this->engineImportRun->fresh();

        if ($run === null) {
            return;
        }

        $run->update([
            'status' => 'running',
            'message' => 'Чтение JSON-файла...',
            'current_stage' => 'reading_file',
            'error_message' => null,
            'started_at' => now(),
            'finished_at' => null,
            'total_engines' => 0,
            'processed_engines' => 0,
            'stats_new' => 0,
            'stats_updated' => 0,
            'stats_unchanged' => 0,
            'stats_processed' => 0,
        ]);

        try {
            $records = $this->parsePayload($run);
            $totalEngines = count($records);

            $run->update([
                'message' => $totalEngines > 0
                    ? "Подготовлено {$totalEngines} двигателей к импорту."
                    : 'Файл прочитан, двигателей для импорта нет.',
                'current_stage' => 'persisting_engines',
                'total_engines' => $totalEngines,
            ]);

            $stats = $importService->import($records, function (array $stats) use (&$run, $totalEngines): void {
                $run->forceFill([
                    'message' => "Импортировано {$stats['processed_engines']} из {$totalEngines} двигателей.",
                    'processed_engines' => $stats['processed_engines'],
                    'stats_new' => $stats['new'],
                    'stats_updated' => $stats['updated'],
                    'stats_unchanged' => $stats['unchanged'],
                    'stats_processed' => $stats['processed'],
                ])->save();
            });

            $run->update([
                'status' => 'succeeded',
                'message' => 'Импорт двигателей завершен успешно.',
                'current_stage' => 'completed',
                'processed_engines' => $stats['processed_engines'],
                'stats_new' => $stats['new'],
                'stats_updated' => $stats['updated'],
                'stats_unchanged' => $stats['unchanged'],
                'stats_processed' => $stats['processed'],
                'finished_at' => now(),
            ]);

            Log::info('engine_import.succeeded', [
                'engine_import_run_id' => $run->id,
                'user_id' => $run->user_id,
                'total_engines' => $totalEngines,
                'processed_engines' => $stats['processed_engines'],
                'stats' => [
                    'new' => $stats['new'],
                    'updated' => $stats['updated'],
                    'unchanged' => $stats['unchanged'],
                    'processed' => $stats['processed'],
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            $run->update([
                'status' => 'failed',
                'message' => 'Импорт двигателей завершился с ошибкой.',
                'current_stage' => 'failed',
                'error_message' => $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'Во время импорта произошла непредвиденная ошибка.',
                'finished_at' => now(),
            ]);

            Log::error('engine_import.failed', [
                'engine_import_run_id' => $run->id,
                'user_id' => $run->user_id,
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
            ]);
        }
    }

    public function failed(Throwable $exception): void
    {
        $run = $this->engineImportRun->fresh();

        if ($run === null || in_array($run->status, ['succeeded', 'failed'], true)) {
            return;
        }

        $run->update([
            'status' => 'failed',
            'message' => 'Импорт двигателей завершился с ошибкой.',
            'current_stage' => 'failed',
            'error_message' => $exception->getMessage(),
            'finished_at' => now(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parsePayload(EngineImportRun $run): array
    {
        if (!Storage::disk('local')->exists($run->file_path)) {
            throw new RuntimeException('Файл импорта двигателей не найден.');
        }

        $decoded = json_decode(
            Storage::disk('local')->get($run->file_path),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        if (is_array($decoded) && array_is_list($decoded)) {
            return $decoded;
        }

        if (is_array($decoded) && isset($decoded['engines']) && is_array($decoded['engines'])) {
            return array_values($decoded['engines']);
        }

        throw new RuntimeException('JSON должен содержать массив двигателей или ключ engines.');
    }
}
