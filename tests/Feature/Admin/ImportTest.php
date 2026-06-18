<?php

namespace Tests\Feature\Admin;

use App\Jobs\ProcessImportJsonJob;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDealer;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\City;
use App\Models\Dealer;
use App\Models\ImportRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_queue_import_file(): void
    {
        Storage::fake('local');
        Queue::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payload()),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'queued')
            ->assertJsonPath('run.original_file_name', 'import.json');

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->firstOrFail();

        $this->assertSame($user->id, $importRun->user_id);
        $this->assertTrue(Storage::disk('local')->exists($importRun->file_path));

        Queue::assertPushed(ProcessImportJsonJob::class);
    }

    public function test_import_validator_accepts_json_file_with_octet_stream_mime_type(): void
    {
        $validator = Validator::make(
            ['file' => $this->jsonFile($this->payload(), 'models_detailed.json', 'application/octet-stream')],
            ['file' => ['required', 'file', 'extensions:json', 'max:102400']],
        );

        $this->assertTrue($validator->passes(), json_encode($validator->errors()->all(), JSON_UNESCAPED_UNICODE));
    }

    public function test_import_fails_for_malformed_json_payload_with_streaming_parser(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('broken-import.json', '{"cars":['),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'failed')
            ->assertJsonPath('run.error_message', 'Файл не является валидным JSON.');
    }

    public function test_import_fails_when_cars_key_is_missing_in_streaming_payload(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();
        $payload = $this->payload();
        unset($payload['cars']);

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($payload, 'missing-cars.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'failed')
            ->assertJsonPath('run.error_message', 'Передайте массив машин для импорта.');
    }

    public function test_authenticated_user_can_process_import_file_when_queue_runs_sync(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payload()),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 15)
            ->assertJsonPath('run.stats.updated', 0)
            ->assertJsonPath('run.stats.unchanged', 0)
            ->assertJsonPath('run.stats.processed', 15)
            ->assertJsonPath('run.processed_cars', 1)
            ->assertJsonPath('run.total_cars', 1);

        $city = City::query()->firstOrFail();
        $brand = Brand::query()->firstOrFail();
        $car = Car::query()->firstOrFail();
        $dealer = Dealer::query()->firstOrFail();
        $carDealer = CarDealer::query()->firstOrFail();
        $configuration = CarConfiguration::query()->firstOrFail();
        $photoGroup = CarPhotoGroup::query()->firstOrFail();
        $photo = CarPhoto::query()->firstOrFail();
        $group = CarConfigurationGroup::query()->firstOrFail();
        $category = CarConfigurationEquipmentCategory::query()->firstOrFail();

        $this->assertSame('moscow', $city->slug);
        $this->assertSame('tesla', $brand->slug);
        $this->assertSame($brand->id, $car->brand_id);
        $this->assertSame('images/tesla/model-y/cover.jpg', $car->cover_path);
        $this->assertSame('Tesla Store', $dealer->name);
        $this->assertSame($car->id, $carDealer->car_id);
        $this->assertSame($city->id, $carDealer->city_id);
        $this->assertSame($dealer->id, $carDealer->dealer_id);
        $this->assertSame('images/tesla/model-y/gallery/front.jpg', $photo->photo_path);
        $this->assertSame($photoGroup->id, $photo->car_photo_group_id);
        $this->assertSame('5.0', $configuration->acceleration);
        $this->assertSame(0, $group->import_index);
        $this->assertSame($group->id, $category->car_configuration_group_id);

        $this->assertDatabaseCount('cities', 1);
        $this->assertDatabaseCount('brands', 1);
        $this->assertDatabaseCount('dealers', 1);
        $this->assertDatabaseCount('cars', 1);
        $this->assertDatabaseCount('car_dealers', 1);
        $this->assertDatabaseCount('car_crash_tests', 1);
        $this->assertDatabaseCount('car_test_drives', 1);
        $this->assertDatabaseCount('car_reviews', 1);
        $this->assertDatabaseCount('car_photo_groups', 1);
        $this->assertDatabaseCount('car_photos', 2);
        $this->assertDatabaseCount('car_configuration_groups', 1);
        $this->assertDatabaseCount('car_configurations', 1);
        $this->assertDatabaseCount('car_configuration_equipment_categories', 1);
        $this->assertDatabaseCount('car_configuration_equipment', 1);
    }

    public function test_job_imports_brands_before_cities_and_cars(): void
    {
        Storage::fake('local');

        /** @var User $user */
        $user = User::factory()->create();
        $path = 'imports/ordered-import.json';
        Storage::disk('local')->put(
            $path,
            json_encode($this->payload(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'queued',
            'original_file_name' => 'ordered-import.json',
            'file_path' => $path,
            'file_size' => Storage::disk('local')->size($path),
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
        ]);

        $service = new class() extends \App\Services\Import\CarImportService
        {
            /** @var array<int, string> */
            public array $stages = [];

            public function importBrands(array $brandsPayload, ?array $stats = null): array
            {
                $this->stages[] = 'brands';

                return parent::importBrands($brandsPayload, $stats);
            }

            public function importCities(array $citiesPayload, ?array $stats = null): array
            {
                $this->stages[] = 'cities';

                if (! Brand::query()->where('slug', 'tesla')->exists()) {
                    throw new \RuntimeException('Бренд должен быть импортирован до городов.');
                }

                return parent::importCities($citiesPayload, $stats);
            }

            public function importCarsChunk(
                array $carsPayload,
                array $stats,
                ?callable $afterCarProcessed = null,
                ?callable $shouldStop = null,
            ): array {
                $this->stages[] = 'cars';

                if (! Brand::query()->where('slug', 'tesla')->exists()) {
                    throw new \RuntimeException('Бренд должен быть импортирован до машин.');
                }

                if (! City::query()->where('slug', 'moscow')->exists()) {
                    throw new \RuntimeException('Город должен быть импортирован до машин.');
                }

                return parent::importCarsChunk($carsPayload, $stats, $afterCarProcessed, $shouldStop);
            }
        };

        (new ProcessImportJsonJob($importRun))->handle($service);

        $importRun->refresh();

        $this->assertSame('succeeded', $importRun->status);
        $this->assertSame(['brands', 'cities', 'cars'], $service->stages);
    }

    public function test_import_marks_records_as_unchanged_when_payload_matches(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payload()),
        ])->assertAccepted();

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payload(), 'second-import.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 0)
            ->assertJsonPath('run.stats.updated', 0)
            ->assertJsonPath('run.stats.unchanged', 15)
            ->assertJsonPath('run.stats.processed', 15);
    }

    public function test_import_reuses_existing_dealer_when_payload_name_differs_by_case(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();
        /** @var Dealer $existingDealer */
        $existingDealer = Dealer::query()->create([
            'name' => 'Tesla Store',
        ]);

        $payload = $this->payload();
        $payload['cars'][0]['dealers'][0]['name'] = 'TESLA STORE';

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($payload, 'case-mismatch-import.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 14)
            ->assertJsonPath('run.stats.updated', 0)
            ->assertJsonPath('run.stats.unchanged', 1)
            ->assertJsonPath('run.stats.processed', 15);

        $carDealer = CarDealer::query()->firstOrFail();

        $this->assertSame($existingDealer->id, $carDealer->dealer_id);
        $this->assertDatabaseCount('dealers', 1);
        $this->assertDatabaseHas('dealers', [
            'id' => $existingDealer->id,
            'name' => 'Tesla Store',
        ]);
    }

    public function test_import_updates_changed_records_without_deleting_missing_nested_entries(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payload()),
        ])->assertAccepted();

        $updatedPayload = $this->payload();
        $updatedPayload['cars'][0]['start_price'] = 53000;
        $updatedPayload['cars'][0]['crash_test']['rating'] = 4;
        $updatedPayload['cars'][0]['groups'][0]['items'][0]['price'] = 56000;
        $updatedPayload['cars'][0]['groups'][0]['equipment'][0]['items'][0]['price'] = 2500;
        unset($updatedPayload['cars'][0]['reviews']);

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($updatedPayload, 'updated-import.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 0)
            ->assertJsonPath('run.stats.updated', 4)
            ->assertJsonPath('run.stats.unchanged', 10)
            ->assertJsonPath('run.stats.processed', 14);

        $car = Car::query()->firstOrFail();
        $crashTest = CarCrashTest::query()->firstOrFail();
        $configuration = CarConfiguration::query()->firstOrFail();
        $equipment = CarConfigurationEquipment::query()->firstOrFail();

        $this->assertSame(53000, $car->start_price);
        $this->assertSame(4, $crashTest->rating);
        $this->assertSame(56000, $configuration->price);
        $this->assertSame(2500, $equipment->price);
        $this->assertDatabaseCount('car_reviews', 1);
    }

    public function test_import_converts_acceleration_from_milliseconds_to_seconds(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);

        /** @var User $user */
        $user = User::factory()->create();

        $payload = $this->payload();
        $payload['cars'][0]['groups'][0]['items'][0]['acceleration'] = 2495;

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($payload, 'import-acceleration-ms.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded');

        $configuration = CarConfiguration::query()->firstOrFail();

        $this->assertSame('2.5', $configuration->acceleration);
    }

    public function test_import_allows_payload_without_brands_when_brand_exists(): void
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

        $payload = $this->payload();
        unset($payload['brands']);

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($payload, 'import-without-brands.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.stats.new', 14)
            ->assertJsonPath('run.stats.updated', 0)
            ->assertJsonPath('run.stats.unchanged', 0)
            ->assertJsonPath('run.stats.processed', 14);

        $this->assertDatabaseCount('brands', 1);
        $this->assertDatabaseCount('cars', 1);
    }

    public function test_import_status_is_available_only_to_owner(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->create([
            'user_id' => $owner->id,
            'status' => 'queued',
            'original_file_name' => 'import.json',
            'file_path' => 'imports/import.json',
            'file_size' => 1024,
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
        ]);

        $this->actingAs($anotherUser)
            ->getJson(route('admin.import.status', $importRun))
            ->assertForbidden();

        $this->actingAs($owner)
            ->getJson(route('admin.import.status', $importRun))
            ->assertOk()
            ->assertJsonPath('run.id', $importRun->id)
            ->assertJsonPath('run.status', 'queued');
    }

    public function test_import_page_includes_latest_active_run_for_owner(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        ImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'succeeded',
            'original_file_name' => 'old.json',
            'file_path' => 'imports/old.json',
            'file_size' => 512,
            'message' => 'Импорт завершен успешно.',
        ]);

        /** @var ImportRun $activeRun */
        $activeRun = ImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'running',
            'original_file_name' => 'active.json',
            'file_path' => 'imports/active.json',
            'file_size' => 1024,
            'message' => 'Импортировано 10 из 100 машин.',
            'total_cars' => 100,
            'processed_cars' => 10,
            'stats_new' => 3,
            'stats_updated' => 4,
            'stats_unchanged' => 5,
            'stats_processed' => 12,
        ]);

        $response = $this->actingAs($user)->get(route('admin.import.index'));

        $response
            ->assertOk()
            ->assertViewHas('page', function (array $page) use ($activeRun): bool {
                return data_get($page, 'props.activeRun.id') === $activeRun->id
                    && data_get($page, 'props.activeRun.status') === 'running'
                    && data_get($page, 'props.activeRun.original_file_name') === 'active.json';
            });
    }

    public function test_import_page_does_not_include_another_users_active_run(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        ImportRun::query()->create([
            'user_id' => $anotherUser->id,
            'status' => 'queued',
            'original_file_name' => 'another-user.json',
            'file_path' => 'imports/another-user.json',
            'file_size' => 1024,
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
        ]);

        $response = $this->actingAs($owner)->get(route('admin.import.index'));

        $response
            ->assertOk()
            ->assertViewHas('page', function (array $page): bool {
                return data_get($page, 'props.activeRun') === null;
            });
    }

    public function test_owner_can_request_stop_for_queued_import(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->create([
            'user_id' => $owner->id,
            'status' => 'queued',
            'original_file_name' => 'import.json',
            'file_path' => 'imports/import.json',
            'file_size' => 1024,
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
        ]);

        $this->actingAs($anotherUser)
            ->postJson(route('admin.import.stop', $importRun))
            ->assertForbidden();

        $this->actingAs($owner)
            ->postJson(route('admin.import.stop', $importRun))
            ->assertAccepted()
            ->assertJsonPath('run.id', $importRun->id)
            ->assertJsonPath('run.stop_requested_at', fn (mixed $value): bool => is_string($value) && $value !== '');

        $this->assertDatabaseHas('import_runs', [
            'id' => $importRun->id,
            'status' => 'queued',
        ]);
    }

    public function test_job_marks_import_as_cancelled_if_stop_requested_before_start(): void
    {
        Storage::fake('local');
        Log::spy();

        /** @var User $user */
        $user = User::factory()->create();

        $path = 'imports/stopped-before-start.json';
        Storage::disk('local')->put(
            $path,
            json_encode($this->payload(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'queued',
            'original_file_name' => 'stopped-before-start.json',
            'file_path' => $path,
            'file_size' => Storage::disk('local')->size($path),
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
            'stop_requested_at' => now(),
        ]);

        (new ProcessImportJsonJob($importRun))->handle(app(\App\Services\Import\CarImportService::class));

        $importRun->refresh();

        $this->assertSame('cancelled', $importRun->status);
        $this->assertNotNull($importRun->finished_at);
        $this->assertDatabaseCount('cars', 0);

        Log::shouldHaveReceived('info')->withArgs(function (string $event, array $context) use ($importRun): bool {
            return $event === 'import.cancelled' && ($context['import_run_id'] ?? null) === $importRun->id;
        })->once();
    }

    public function test_car_import_service_stops_after_current_car_when_stop_is_requested(): void
    {
        $service = app(\App\Services\Import\CarImportService::class);
        $payload = $this->payloadWithTwoCars();
        $stopRequested = false;

        $stats = $service->import(
            $payload,
            function (array $stats) use (&$stopRequested): void {
                if ($stats['processed_cars'] === 1) {
                    $stopRequested = true;
                }
            },
            null,
            function () use (&$stopRequested): bool {
                return $stopRequested;
            },
        );

        $this->assertSame(1, $stats['processed_cars']);
        $this->assertDatabaseCount('cars', 1);
    }

    public function test_job_writes_stage_logs_with_import_run_context(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);
        Log::spy();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payload(), 'logs-import.json'),
        ]);

        $response->assertAccepted();
        $importRunId = (int) $response->json('run.id');

        Log::shouldHaveReceived('info')->withArgs(function (string $event, array $context) use ($importRunId): bool {
            return $event === 'import.stage.validation_ok' && ($context['import_run_id'] ?? null) === $importRunId;
        })->once();

        Log::shouldHaveReceived('info')->withArgs(function (string $event, array $context) use ($importRunId): bool {
            return $event === 'import.succeeded' && ($context['import_run_id'] ?? null) === $importRunId;
        })->once();
    }

    public function test_import_writes_chunk_logs_for_large_payload(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);
        Log::spy();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($this->payloadWithCarsCount(120), 'chunked-import.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'succeeded')
            ->assertJsonPath('run.processed_cars', 120);

        $importRunId = (int) $response->json('run.id');

        Log::shouldHaveReceived('info')->withArgs(function (string $event, array $context) use ($importRunId): bool {
            return $event === 'import.stage.chunk_validation_started'
                && ($context['import_run_id'] ?? null) === $importRunId
                && ($context['chunk_index'] ?? null) === 2;
        })->once();

        Log::shouldHaveReceived('info')->withArgs(function (string $event, array $context) use ($importRunId): bool {
            return $event === 'import.stage.chunk_persist_completed'
                && ($context['import_run_id'] ?? null) === $importRunId
                && ($context['chunk_index'] ?? null) === 2;
        })->once();
    }

    public function test_import_fails_with_chunk_validation_error_context(): void
    {
        Storage::fake('local');
        config(['queue.default' => 'sync']);
        Log::spy();

        /** @var User $user */
        $user = User::factory()->create();
        $payload = $this->payloadWithCarsCount(120);
        unset($payload['cars'][105]['slug']);

        $response = $this->actingAs($user)->post(route('admin.import.store'), [
            'file' => $this->jsonFile($payload, 'chunked-import-invalid.json'),
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('run.status', 'failed')
            ->assertJsonPath('run.error_message', fn (mixed $value): bool => is_string($value) && str_contains($value, 'машины #106'));

        $importRunId = (int) $response->json('run.id');

        Log::shouldHaveReceived('warning')->withArgs(function (string $event, array $context) use ($importRunId): bool {
            return $event === 'import.stage.chunk_validation_failed'
                && ($context['import_run_id'] ?? null) === $importRunId
                && ($context['chunk_index'] ?? null) === 2
                && ($context['car_index'] ?? null) === 106;
        })->once();
    }

    public function test_job_can_stop_between_chunks(): void
    {
        Storage::fake('local');

        /** @var User $user */
        $user = User::factory()->create();
        $path = 'imports/chunk-stop.json';
        Storage::disk('local')->put(
            $path,
            json_encode($this->payloadWithCarsCount(120), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );

        /** @var ImportRun $importRun */
        $importRun = ImportRun::query()->create([
            'user_id' => $user->id,
            'status' => 'queued',
            'original_file_name' => 'chunk-stop.json',
            'file_path' => $path,
            'file_size' => Storage::disk('local')->size($path),
            'message' => 'Файл загружен. Импорт поставлен в очередь.',
        ]);

        $service = new class($importRun->id) extends \App\Services\Import\CarImportService
        {
            public function __construct(
                private readonly int $importRunId,
            ) {
            }

            /**
             * @param  array<int, array<string, mixed>>  $carsPayload
             * @param  array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}  $stats
             * @param  null|callable(array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}): void  $afterCarProcessed
             * @param  null|callable(): bool  $shouldStop
             * @return array{new: int, updated: int, unchanged: int, processed: int, processed_cars: int}
             */
            public function importCarsChunk(
                array $carsPayload,
                array $stats,
                ?callable $afterCarProcessed = null,
                ?callable $shouldStop = null,
            ): array {
                $updatedStats = parent::importCarsChunk($carsPayload, $stats, $afterCarProcessed, $shouldStop);

                ImportRun::query()
                    ->whereKey($this->importRunId)
                    ->whereNull('stop_requested_at')
                    ->update(['stop_requested_at' => now()]);

                return $updatedStats;
            }
        };

        (new ProcessImportJsonJob($importRun))->handle($service);

        $importRun->refresh();

        $this->assertSame('cancelled', $importRun->status);
        $this->assertSame(100, $importRun->processed_cars);
        $this->assertNotNull($importRun->stop_requested_at);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function jsonFile(
        array $payload,
        string $name = 'import.json',
        ?string $mimeType = null,
    ): UploadedFile
    {
        if ($mimeType !== null) {
            return UploadedFile::fake()->create(
                $name,
                1,
                $mimeType,
            );
        }

        $file = UploadedFile::fake()->createWithContent(
            $name,
            json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );

        return $file;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'cities' => [
                [
                    'name' => 'Москва',
                    'slug' => 'moscow',
                ],
            ],
            'brands' => [
                [
                    'name' => 'Tesla',
                    'slug' => 'tesla',
                    'leave_from_russian' => true,
                ],
            ],
            'cars' => [
                [
                    'name' => 'Model Y',
                    'slug' => 'model-y',
                    'brand_slug' => 'tesla',
                    'year' => '2024',
                    'is_electric_car' => true,
                    'is_soon' => false,
                    'is_another_models' => false,
                    'start_price' => 50000,
                    'end_price' => 65000,
                    'cover_path' => '/images/tesla/model-y/cover.jpg',
                    'crash_test' => [
                        'year' => 2024,
                        'rating' => 5,
                        'video_path' => '/videos/model-y-crash',
                    ],
                    'test_drives' => [
                        [
                            'author' => 'Autoblog',
                            'path' => '/test-drives/model-y',
                        ],
                    ],
                    'reviews' => [
                        [
                            'type' => 'good',
                            'value' => 'Быстрый и просторный',
                        ],
                    ],
                    'photo_groups' => [
                        [
                            'name' => 'Экстерьер',
                            'photo_list' => [
                                '/images/tesla/model-y/gallery/front.jpg',
                                '/images/tesla/model-y/gallery/rear.jpg',
                            ],
                        ],
                    ],
                    'dealers' => [
                        [
                            'name' => 'Tesla Store',
                            'city_slug' => 'moscow',
                            'is_official_deler' => true,
                            'address' => 'Ленинградский проспект, 10',
                            'phone' => '+7 (495) 000-00-00',
                            'url' => 'https://example.com/tesla-store',
                        ],
                    ],
                    'groups' => [
                        [
                            'name' => 'Long Range',
                            'order' => 1,
                            'items' => [
                                [
                                    'price' => 54000,
                                    'engine_type' => 'electric',
                                    'horsepower' => 384,
                                    'transmission' => 'single-speed',
                                    'drive_type' => 'awd',
                                    'fuel_city' => 0,
                                    'fuel_highway' => 0,
                                    'fuel_combined' => 0,
                                    'acceleration' => 5.0,
                                    'speed' => 217,
                                ],
                            ],
                            'equipment' => [
                                [
                                    'name' => 'Комфорт',
                                    'items' => [
                                        [
                                            'value' => 'Подогрев сидений',
                                            'is_extension' => false,
                                            'price' => 2000,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadWithTwoCars(): array
    {
        $payload = $this->payload();

        $payload['cars'][] = [
            ...$payload['cars'][0],
            'name' => 'Model 3',
            'slug' => 'model-3',
        ];

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadWithCarsCount(int $carsCount): array
    {
        $payload = [
            'cities' => [],
            'brands' => [
                [
                    'name' => 'Tesla',
                    'slug' => 'tesla',
                    'leave_from_russian' => true,
                ],
            ],
            'cars' => [],
        ];

        for ($index = 1; $index <= $carsCount; $index++) {
            $payload['cars'][] = [
                'name' => "Model {$index}",
                'slug' => "model-{$index}",
                'brand_slug' => 'tesla',
                'year' => '2024',
                'is_electric_car' => true,
                'is_soon' => false,
                'is_another_models' => false,
                'start_price' => 40000 + $index,
                'end_price' => 50000 + $index,
            ];
        }

        return $payload;
    }
}
