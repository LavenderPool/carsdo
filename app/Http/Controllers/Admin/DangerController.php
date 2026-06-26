<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchCarWebpBackfillJob;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDealer;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\City;
use App\Models\Dealer;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use App\Models\MediaAlias;
use App\Support\Cache\SiteCache;
use App\Support\Media\CarWebpBackfillService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DangerController extends Controller
{
    public function clearCache(): RedirectResponse
    {
        Artisan::call('optimize:clear');

        return redirect()
            ->back()
            ->with('success', 'Кеш очищен.');
    }

    public function setLocalIds(Request $request): Response
    {
        return Inertia::render('Admin/Danger/SetLocalIds', [
            ...$this->buildSetLocalIdsPreview(),
            'flash' => $this->flashPayload($request),
        ]);
    }

    public function applySetLocalIds(): RedirectResponse
    {
        $preview = $this->buildSetLocalIdsPreview();

        if ($preview['configurationsCount'] === 0) {
            return redirect()
                ->route('admin.danger.set-local-ids')
                ->with('success', 'Конфигурации с пустым local_id не найдены.');
        }

        $updatedConfigurationsCount = 0;
        $updatedCarIds = [];
        $timestamp = now();

        DB::transaction(function () use ($preview, $timestamp, &$updatedConfigurationsCount, &$updatedCarIds): void {
            foreach ($preview['cars'] as $carPreview) {
                foreach ($carPreview['configurations'] as $configurationPreview) {
                    $updated = CarConfiguration::query()
                        ->whereKey($configurationPreview['id'])
                        ->whereNull('local_id')
                        ->update([
                            'local_id' => $configurationPreview['new_local_id'],
                            'updated_at' => $timestamp,
                        ]);

                    if ($updated === 1) {
                        $updatedConfigurationsCount++;
                        $updatedCarIds[$carPreview['car_id']] = true;
                    }
                }
            }
        });

        if ($updatedConfigurationsCount === 0) {
            return redirect()
                ->route('admin.danger.set-local-ids')
                ->with('success', 'Изменения не применены: local_id уже были заполнены.');
        }

        return redirect()
            ->route('admin.danger.set-local-ids')
            ->with(
                'success',
                sprintf(
                    'local_id заполнены у %d конфигураций в %d автомобилях.',
                    $updatedConfigurationsCount,
                    count($updatedCarIds),
                ),
            );
    }

    public function webpConvert(Request $request, CarWebpBackfillService $backfillService): Response
    {
        return Inertia::render('Admin/Danger/WebpConvert', [
            ...$backfillService->buildPreview(),
            'isRunning' => Cache::has(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY),
            'flash' => $this->flashPayload($request),
        ]);
    }

    public function applyWebpConvert(CarWebpBackfillService $backfillService): RedirectResponse
    {
        $preview = $backfillService->buildPreview();
        $pending = $preview['summary']['pending'];

        if ($pending === 0) {
            return redirect()
                ->route('admin.danger.webp-convert')
                ->with('success', 'Изображения без WebP не найдены.');
        }

        if (!Cache::add(DispatchCarWebpBackfillJob::RUNNING_CACHE_KEY, true, now()->addHour())) {
            return redirect()
                ->route('admin.danger.webp-convert')
                ->with('warning', 'Конвертация уже запущена.');
        }

        DispatchCarWebpBackfillJob::dispatch();

        return redirect()
            ->route('admin.danger.webp-convert')
            ->with('success', "Конвертация поставлена в очередь для {$pending} изображений.");
    }

    public const CONVERT_PRICE_THRESHOLD = 500000;

    public const CONVERT_TARGET_CURRENCY = '$';

    public function convert(Request $request): Response
    {
        return Inertia::render('Admin/Danger/Convert', [
            ...$this->buildConvertPreview(),
            'flash' => $this->flashPayload($request),
        ]);
    }

    public function applyConvert(): RedirectResponse
    {
        $updated = CarConfiguration::query()
            ->where('price', '<', self::CONVERT_PRICE_THRESHOLD)
            ->where(function ($query): void {
                $query
                    ->whereNull('currency')
                    ->orWhere('currency', '!=', self::CONVERT_TARGET_CURRENCY);
            })
            ->update([
                'currency' => self::CONVERT_TARGET_CURRENCY,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return redirect()
                ->route('admin.danger.convert')
                ->with('success', 'Конфигурации для смены валюты не найдены.');
        }

        SiteCache::flush();

        return redirect()
            ->route('admin.danger.convert')
            ->with(
                'success',
                sprintf('Валюта изменена на %s у %d конфигураций.', self::CONVERT_TARGET_CURRENCY, $updated),
            );
    }

    public function fullClear(): RedirectResponse
    {
        DB::transaction(function (): void {
            CarConfigurationEquipment::query()->delete();
            CarConfigurationEquipmentCategory::query()->delete();
            CarConfiguration::query()->delete();
            CarConfigurationGroup::query()->delete();
            CarPhoto::query()->delete();
            CarPhotoGroup::query()->delete();
            CarCrashTest::query()->delete();
            CarReview::query()->delete();
            CarTestDrive::query()->delete();
            CarDealer::query()->delete();
            MediaAlias::query()->delete();
            Car::query()->delete();
            Dealer::query()->delete();
            City::query()->delete();
            Brand::withTrashed()->forceDelete();
        });

        Storage::disk('public')->deleteDirectory('covers');
        Storage::disk('public')->deleteDirectory('images');

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Все бренды, автомобили и связанные записи удалены.');
    }

    /**
     * @return array{
     *     threshold: int,
     *     targetCurrency: string,
     *     carsCount: int,
     *     configurationsCount: int,
     *     cars: array<int, array{
     *         car_id: int,
     *         car_name: string,
     *         brand_name: string|null,
     *         slug: string|null,
     *         brand_slug: string|null,
     *         configurations_count: int,
     *         configurations: array<int, array{
     *             id: int,
     *             price: int|null,
     *             current_currency: string|null
     *         }>
     *     }>
     * }
     */
    private function buildConvertPreview(): array
    {
        $configurations = CarConfiguration::query()
            ->with([
                'car' => fn ($query) => $query->select(['id', 'brand_id', 'name', 'slug']),
                'car.brand:id,name,slug',
            ])
            ->where('price', '<', self::CONVERT_PRICE_THRESHOLD)
            ->where(function ($query): void {
                $query
                    ->whereNull('currency')
                    ->orWhere('currency', '!=', self::CONVERT_TARGET_CURRENCY);
            })
            ->orderBy('car_id')
            ->orderBy('id')
            ->get(['id', 'car_id', 'price', 'currency']);

        $cars = $configurations
            ->groupBy('car_id')
            ->map(function (Collection $items, int|string $carId): array {
                /** @var CarConfiguration $first */
                $first = $items->first();

                return [
                    'car_id' => (int) $carId,
                    'car_name' => $first->car?->name ?? 'Автомобиль #'.(int) $carId,
                    'brand_name' => $first->car?->brand?->name,
                    'slug' => $first->car?->slug,
                    'brand_slug' => $first->car?->brand?->slug,
                    'configurations_count' => $items->count(),
                    'configurations' => $items
                        ->values()
                        ->map(fn (CarConfiguration $configuration): array => [
                            'id' => $configuration->id,
                            'price' => $configuration->price !== null ? (int) $configuration->price : null,
                            'current_currency' => $configuration->currency,
                        ])
                        ->all(),
                ];
            })
            ->values()
            ->all();

        return [
            'threshold' => self::CONVERT_PRICE_THRESHOLD,
            'targetCurrency' => self::CONVERT_TARGET_CURRENCY,
            'carsCount' => count($cars),
            'configurationsCount' => $configurations->count(),
            'cars' => $cars,
        ];
    }

    /**
     * @return array{
     *     carsCount: int,
     *     configurationsCount: int,
     *     cars: array<int, array{
     *         car_id: int,
     *         car_name: string,
     *         brand_name: string|null,
     *         slug: string|null,
     *         brand_slug: string|null,
     *         configurations_count: int,
     *         has_conflicts: bool,
     *         starting_local_id: int,
     *         existing_local_ids: array<int, int>,
     *         configurations: array<int, array{
     *             id: int,
     *             car_configuration_group_id: int,
     *             current_local_id: null,
     *             new_local_id: int
     *         }>
     *     }>
     * }
     */
    private function buildSetLocalIdsPreview(): array
    {
        $configurations = CarConfiguration::query()
            ->with([
                'car' => fn ($query) => $query->select(['id', 'brand_id', 'name', 'slug']),
                'car.brand:id,name,slug',
            ])
            ->whereNull('local_id')
            ->where('have_page', true)
            ->orderBy('car_id')
            ->orderBy('id')
            ->get([
                'id',
                'car_id',
                'car_configuration_group_id',
                'local_id',
                'have_page',
            ]);

        if ($configurations->isEmpty()) {
            return [
                'carsCount' => 0,
                'configurationsCount' => 0,
                'cars' => [],
            ];
        }

        $affectedCarIds = $configurations
            ->pluck('car_id')
            ->unique()
            ->values()
            ->all();

        /** @var Collection<int, array<int, int>> $existingLocalIdsByCar */
        $existingLocalIdsByCar = CarConfiguration::query()
            ->whereIntegerInRaw('car_id', $affectedCarIds, 'and', false)
            ->whereNotNull('local_id')
            ->orderBy('car_id')
            ->orderBy('local_id')
            ->get(['car_id', 'local_id'])
            ->groupBy('car_id')
            ->map(
                fn (Collection $items): array => $items
                    ->pluck('local_id')
                    ->filter(static fn ($value): bool => $value !== null)
                    ->map(static fn ($value): int => (int) $value)
                    ->values()
                    ->all(),
            );

        $cars = $configurations
            ->groupBy('car_id')
            ->map(function (Collection $items, int|string $carId) use ($existingLocalIdsByCar): array {
                /** @var CarConfiguration $firstConfiguration */
                $firstConfiguration = $items->first();
                $existingLocalIds = $existingLocalIdsByCar->get((int) $carId, []);
                $newLocalIds = $this->allocateLocalIds($existingLocalIds, $items->count());
                $proposedInitialLocalIds = range(1, count($newLocalIds));
                $hasConflicts = array_intersect($proposedInitialLocalIds, $existingLocalIds) !== [];
                $startingLocalId = $newLocalIds[0];

                return [
                    'car_id' => (int) $carId,
                    'car_name' => $firstConfiguration->car?->name ?? 'Автомобиль #'.(int) $carId,
                    'brand_name' => $firstConfiguration->car?->brand?->name,
                    'slug' => $firstConfiguration->car?->slug,
                    'brand_slug' => $firstConfiguration->car?->brand?->slug,
                    'configurations_count' => $items->count(),
                    'has_conflicts' => $hasConflicts,
                    'starting_local_id' => $startingLocalId,
                    'existing_local_ids' => $existingLocalIds,
                    'configurations' => $items
                        ->values()
                        ->map(
                            fn (CarConfiguration $configuration, int $index): array => [
                                'id' => $configuration->id,
                                'car_configuration_group_id' => $configuration->car_configuration_group_id,
                                'current_local_id' => null,
                                'new_local_id' => $newLocalIds[$index],
                            ],
                        )
                        ->all(),
                ];
            })
            ->values()
            ->all();

        return [
            'carsCount' => count($cars),
            'configurationsCount' => $configurations->count(),
            'cars' => $cars,
        ];
    }

    /**
     * @param  array<int, int>  $existingLocalIds
     * @return array<int, int>
     */
    private function allocateLocalIds(array $existingLocalIds, int $count): array
    {
        $occupied = array_fill_keys($existingLocalIds, true);
        $newLocalIds = [];
        $candidate = 1;

        while (count($newLocalIds) < $count) {
            if (!isset($occupied[$candidate])) {
                $newLocalIds[] = $candidate;
            }

            $candidate++;
        }

        return $newLocalIds;
    }

    /**
     * @return array{success: string|null, warning: string|null}
     */
    private function flashPayload(Request $request): array
    {
        return [
            'success' => $request->session()->get('success'),
            'warning' => $request->session()->get('warning'),
        ];
    }
}
