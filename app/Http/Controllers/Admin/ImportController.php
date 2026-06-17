<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportUploadRequest;
use App\Jobs\ProcessImportJsonJob;
use App\Models\ImportRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var ImportRun|null $activeRun */
        $activeRun = ImportRun::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['queued', 'running'])
            ->latest('id')
            ->first();

        return Inertia::render('Admin/Import/Index', [
            'activeRun' => $activeRun ? $this->serializeRun($activeRun) : null,
        ]);
    }

    public function store(ImportUploadRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');
        $storedPath = $file->store('imports', 'local');

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->create([
            'user_id' => $request->user()->id,
            'status' => 'queued',
            'original_file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_size' => $file->getSize() ?? Storage::disk('local')->size($storedPath),
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
        ]);

        ProcessImportJsonJob::dispatch($importRun);
        $importRun->refresh();

        Log::info('import.queued', [
            'import_run_id' => $importRun->id,
            'correlation_id' => (string) $importRun->id,
            'user_id' => $importRun->user_id,
            'status' => $importRun->status,
            'original_file_name' => $importRun->original_file_name,
            'file_size' => $importRun->file_size,
            'file_path' => $importRun->file_path,
        ]);

        return response()->json([
            'message' => 'Импорт запущен.',
            'run' => $this->serializeRun($importRun),
        ], 202);
    }

    public function status(Request $request, ImportRun $importRun): JsonResponse
    {
        abort_unless($request->user()?->id === $importRun->user_id, 403);

        return response()->json([
            'run' => $this->serializeRun($importRun),
        ]);
    }

    public function stop(Request $request, ImportRun $importRun): JsonResponse
    {
        abort_unless($request->user()?->id === $importRun->user_id, 403);

        if (!in_array($importRun->status, ['queued', 'running'], true)) {
            return response()->json([
                'message' => 'Импорт уже завершен.',
                'run' => $this->serializeRun($importRun),
            ]);
        }

        if ($importRun->stop_requested_at === null) {
            $importRun->update([
                'stop_requested_at' => now(),
                'message' => 'Остановка импорта запрошена. Ожидаем завершения текущего шага.',
            ]);
        }

        return response()->json([
            'message' => 'Остановка импорта запрошена.',
            'run' => $this->serializeRun($importRun->fresh() ?? $importRun),
        ], 202);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeRun(ImportRun $importRun): array
    {
        return [
            'id' => $importRun->id,
            'status' => $importRun->status,
            'original_file_name' => $importRun->original_file_name,
            'file_size' => $importRun->file_size,
            'message' => $importRun->message,
            'total_cars' => $importRun->total_cars,
            'processed_cars' => $importRun->processed_cars,
            'stats' => [
                'new' => $importRun->stats_new,
                'updated' => $importRun->stats_updated,
                'unchanged' => $importRun->stats_unchanged,
                'processed' => $importRun->stats_processed,
            ],
            'error_message' => $importRun->error_message,
            'started_at' => $importRun->started_at?->toIso8601String(),
            'finished_at' => $importRun->finished_at?->toIso8601String(),
            'stop_requested_at' => $importRun->stop_requested_at?->toIso8601String(),
            'created_at' => $importRun->created_at?->toIso8601String(),
            'updated_at' => $importRun->updated_at?->toIso8601String(),
        ];
    }
}
