<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Engine;
use App\Models\EngineImportRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EngineImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_process_engine_import_file_when_queue_runs_sync(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();
        Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.engines.import.store'), [
            'file' => $this->jsonFile($this->payload()),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 1)
            ->assertJsonPath('run.stats.updated', 0)
            ->assertJsonPath('run.stats.unchanged', 0)
            ->assertJsonPath('run.stats.processed', 1)
            ->assertJsonPath('run.processed_engines', 1)
            ->assertJsonPath('run.total_engines', 1);

        $engine = Engine::query()->firstOrFail();

        $this->assertSame('tesla', $engine->brand->slug);
        $this->assertSame('model-y-long-range', $engine->slug);
        $this->assertSame('Long Range', $engine->name);
        $this->assertSame('electric', $engine->engine_type);
        $this->assertSame('1498', $engine->displacement_cc);
        $this->assertSame('450', $engine->max_horsepower);
        $this->assertTrue($engine->has_start_stop_system);
        $this->assertSame('Подробное описание двигателя', $engine->page_text);
    }

    public function test_engine_import_skips_records_for_missing_brand(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.engines.import.store'), [
            'file' => $this->jsonFile($this->payload()),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 0)
            ->assertJsonPath('run.stats.updated', 0)
            ->assertJsonPath('run.stats.unchanged', 0)
            ->assertJsonPath('run.stats.processed', 0)
            ->assertJsonPath('run.processed_engines', 1)
            ->assertJsonPath('run.total_engines', 1);

        $this->assertDatabaseCount('engines', 0);
    }

    public function test_engine_import_updates_existing_engine_without_duplicates(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();
        /** @var Brand $brand */
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => true,
        ]);

        /** @var Engine $engine */
        $engine = Engine::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Long Range',
            'slug' => 'model-y-long-range',
            'engine_type' => 'electric',
            'max_horsepower' => '450',
        ]);

        $payload = $this->payload();
        $payload['engines'][0]['Максимальная мощность, л.с.'] = '480';

        $response = $this->actingAs($user)->post(route('admin.engines.import.store'), [
            'file' => $this->jsonFile($payload, 'engines-update.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 0)
            ->assertJsonPath('run.stats.updated', 1)
            ->assertJsonPath('run.stats.unchanged', 0)
            ->assertJsonPath('run.stats.processed', 1);

        $engine->refresh();

        $this->assertSame('480', $engine->max_horsepower);
        $this->assertDatabaseCount('engines', 1);
    }

    public function test_engine_import_truncates_oversized_short_spec_values(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();
        Brand::query()->create([
            'name' => 'BMW',
            'slug' => 'bmw',
            'leave_from_russian' => false,
        ]);

        $payload = [
            'engines' => [
                [
                    'brand_slug' => 'bmw',
                    'engine_slug' => 'b48b20',
                    'engine_name' => 'BMW B48B20',
                    'Максимальная мощность, л.с. (кВт) при об./мин.' => str_repeat('1', 300),
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('admin.engines.import.store'), [
            'file' => $this->jsonFile($payload, 'engines-overflow.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded');

        $engine = Engine::query()->firstOrFail();

        $this->assertSame(255, mb_strlen((string) $engine->max_power_output_at_rpm));
        $this->assertSame(str_repeat('1', 255), $engine->max_power_output_at_rpm);
    }

    public function test_import_page_includes_latest_active_run_for_owner(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        EngineImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'succeeded',
            'original_file_name' => 'old.json',
            'file_path' => 'engine-imports/old.json',
            'file_size' => 512,
            'message' => 'Импорт завершен успешно.',
        ]);

        /** @var EngineImportRun $activeRun */
        $activeRun = EngineImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'running',
            'original_file_name' => 'active.json',
            'file_path' => 'engine-imports/active.json',
            'file_size' => 1024,
            'message' => 'Импортировано 2 из 5 двигателей.',
            'current_stage' => 'persisting_engines',
            'total_engines' => 5,
            'processed_engines' => 2,
            'stats_new' => 1,
            'stats_updated' => 1,
            'stats_unchanged' => 0,
            'stats_processed' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('admin.engines.import.index'));

        $response
            ->assertOk()
            ->assertViewHas('page', function (array $page) use ($activeRun): bool {
                return data_get($page, 'props.activeRun.id') === $activeRun->id
                    && data_get($page, 'props.activeRun.status') === 'running'
                    && data_get($page, 'props.activeRun.processed_engines') === 2;
            });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function jsonFile(
        array $payload,
        string $name = 'engines.json',
    ): UploadedFile {
        return UploadedFile::fake()->createWithContent(
            $name,
            json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'engines' => [
                [
                    'brand_slug' => 'tesla',
                    'engine_slug' => 'model-y-long-range',
                    'engine_name' => 'Long Range',
                    'engine_url' => 'https://example.com/engines/model-y-long-range',
                    'Тип двигателя' => 'electric',
                    'Объем двигателя, куб.см' => 1498,
                    'Максимальная мощность, л.с.' => '450',
                    'Система старт-стоп' => 'Да',
                    'Дополнительная информация о двигателе' => 'Батарейный блок с высоким КПД',
                    'text' => 'Подробное описание двигателя',
                ],
            ],
        ];
    }
}
