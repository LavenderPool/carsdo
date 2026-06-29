<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportUploadRequest;
use App\Jobs\ProcessEngineImportJsonJob;
use App\Models\EngineImportRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EngineImportController extends Controller
{
    public function index(Request $request): Response
    {
        $activeRun = EngineImportRun::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['queued', 'running'])
            ->latest('id')
            ->first();

        return Inertia::render('Admin/Engines/Import', [
            'activeRun' => $activeRun ? $this->serializeRun($activeRun) : null,
        ]);
    }

    public function store(ImportUploadRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');
        $storedPath = $file->store('engine-imports', 'local');

        $run = EngineImportRun::query()->create([
            'user_id' => $request->user()->id,
            'status' => 'queued',
            'original_file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_size' => $file->getSize() ?? Storage::disk('local')->size($storedPath),
            'message' => 'Файл загружен. Импорт двигателей поставлен в очередь.',
        ]);

        ProcessEngineImportJsonJob::dispatch($run);
        $run->refresh();

        return response()->json([
            'message' => 'Импорт двигателей запущен.',
            'run' => $this->serializeRun($run),
        ], 202);
    }

    public function status(Request $request, EngineImportRun $engineImportRun): JsonResponse
    {
        abort_unless($request->user()?->id === $engineImportRun->user_id, 403);

        return response()->json([
            'run' => $this->serializeRun($engineImportRun),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeRun(EngineImportRun $run): array
    {
        return [
            'id' => $run->id,
            'status' => $run->status,
            'original_file_name' => $run->original_file_name,
            'file_size' => $run->file_size,
            'message' => $run->message,
            'current_stage' => $run->current_stage,
            'total_engines' => $run->total_engines,
            'processed_engines' => $run->processed_engines,
            'stats' => [
                'new' => $run->stats_new,
                'updated' => $run->stats_updated,
                'unchanged' => $run->stats_unchanged,
                'processed' => $run->stats_processed,
            ],
            'error_message' => $run->error_message,
            'started_at' => $run->started_at?->toIso8601String(),
            'finished_at' => $run->finished_at?->toIso8601String(),
            'created_at' => $run->created_at?->toIso8601String(),
            'updated_at' => $run->updated_at?->toIso8601String(),
        ];
    }
}
