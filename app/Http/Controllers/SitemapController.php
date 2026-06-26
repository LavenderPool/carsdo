<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarCatalog;
use App\Models\CarCrashTest;
use App\Models\CarPhoto;
use App\Models\CarTestDrive;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect();

        $urls->push($this->entry(url('/'), Car::query()->max('updated_at')));
        $urls->push($this->entry(url('/electric-cars/'), Car::query()->where('is_electric_car', true)->max('updated_at')));
        $urls->push($this->entry(url('/crash-test/'), CarCrashTest::query()->max('updated_at')));
        $urls->push($this->entry(url('/crash-test/electric-cars/'), CarCrashTest::query()
            ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
            ->max('updated_at')));
        $urls->push($this->entry(url('/test-drive/'), CarTestDrive::query()->max('updated_at')));
        $urls->push($this->entry(url('/test-drive/electric-cars/'), CarTestDrive::query()
            ->whereHas('car', static fn ($query) => $query->where('is_electric_car', true))
            ->max('updated_at')));
        $urls->push($this->entry(url('/cars-photo/'), CarPhoto::query()->max('updated_at')));
        $urls->push($this->entry(url('/blog/'), Article::query()->published()->max('updated_at')));

        Article::query()
            ->published()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('published_at')
            ->get()
            ->each(function (Article $article) use ($urls): void {
                $urls->push($this->entry(url("/blog/{$article->slug}/"), $article->updated_at));
            });

        $this->newCarYears()
            ->each(function (string $year) use ($urls): void {
                $lastmod = Car::query()
                    ->where('year', $year)
                    ->where('is_soon', false)
                    ->max('updated_at');

                $urls->push($this->entry(url("/new-cars-{$year}/"), $lastmod));
            });

        CarCatalog::query()
            ->where('is_published', true)
            ->select(['id', 'slug', 'updated_at'])
            ->with(['cars:id,updated_at'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(function (CarCatalog $catalog) use ($urls): void {
                $urls->push($this->entry(
                    url("/catalogs/{$catalog->slug}/"),
                    $this->latestUpdatedAt([$catalog, $catalog->cars]),
                ));
            });

        Brand::query()
            ->select(['id', 'name', 'slug', 'updated_at'])
            ->whereHas('cars')
            ->with([
                'cars' => fn ($query) => $query
                    ->select([
                        'id',
                        'brand_id',
                        'slug',
                        'updated_at',
                    ])
                    ->withCount(['reviews', 'testDrives', 'photos'])
                    ->with([
                        'crashTest:id,car_id,updated_at',
                        'configurationGroups:id,car_id,order,import_index,updated_at',
                        'configurations:id,car_id,car_configuration_group_id,local_id,updated_at',
                    ]),
            ])
            ->orderBy('name')
            ->get()
            ->each(function (Brand $brand) use ($urls): void {
                $urls->push($this->entry(url("/{$brand->slug}/"), $this->latestUpdatedAt([$brand, $brand->cars])));

                if ($brand->cars->contains(fn (Car $car): bool => $car->test_drives_count > 0)) {
                    $urls->push($this->entry(
                        url("/test-drive/{$brand->slug}/"),
                        $this->latestUpdatedAt(
                            $brand->cars->filter(fn (Car $car): bool => $car->test_drives_count > 0)
                        ),
                    ));
                }

                if ($brand->cars->contains(fn (Car $car): bool => $car->crashTest !== null)) {
                    $urls->push($this->entry(
                        url("/crash-test/{$brand->slug}/"),
                        $this->latestUpdatedAt(
                            $brand->cars->filter(fn (Car $car): bool => $car->crashTest !== null)
                        ),
                    ));
                }

                if ($brand->cars->contains(fn (Car $car): bool => $car->photos_count > 0)) {
                    $urls->push($this->entry(
                        url("/cars-photo/{$brand->slug}/"),
                        $this->latestUpdatedAt(
                            $brand->cars->filter(fn (Car $car): bool => $car->photos_count > 0)
                        ),
                    ));
                }

                $brand->cars->each(function (Car $car) use ($brand, $urls): void {
                    $urls->push($this->entry(
                        url("/{$brand->slug}/{$car->slug}/"),
                        $this->latestUpdatedAt([$car, $car->crashTest, $car->configurationGroups]),
                    ));

                    if ($car->reviews_count > 0) {
                        $urls->push($this->entry(
                            url("/{$brand->slug}/{$car->slug}/reviews/"),
                            $this->latestUpdatedAt($car),
                        ));
                    }

                    if ($car->crashTest !== null) {
                        $urls->push($this->entry(
                            url("/{$brand->slug}/{$car->slug}/crash-test/"),
                            $this->latestUpdatedAt([$car, $car->crashTest]),
                        ));
                    }

                    if ($car->test_drives_count > 0) {
                        $urls->push($this->entry(
                            url("/{$brand->slug}/{$car->slug}/test-drive/"),
                            $this->latestUpdatedAt($car),
                        ));
                    }

                    $car->configurations
                        ->filter(fn ($configuration): bool => filled($configuration->local_id))
                        ->sortBy([
                            ['car_configuration_group_id', 'asc'],
                            ['import_index', 'asc'],
                            ['id', 'asc'],
                        ])
                        ->values()
                        ->each(function ($configuration) use ($brand, $car, $urls): void {
                            $group = $car->configurationGroups->firstWhere('id', $configuration->car_configuration_group_id);

                            $urls->push($this->entry(
                                url("/{$brand->slug}/{$car->slug}/equipment-{$configuration->local_id}/"),
                                $this->latestUpdatedAt([$car, $group, $configuration]),
                            ));
                        });
                });
            });

        $xml = $this->renderXml(
            $urls
                ->filter()
                ->unique('loc')
                ->values(),
        );

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function renderXml(Collection $urls): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>'.e($url['loc']).'</loc>';

            if (($url['lastmod'] ?? null) instanceof CarbonInterface) {
                $xml[] = '    <lastmod>'.$url['lastmod']->toAtomString().'</lastmod>';
            }

            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }

    private function entry(string $loc, mixed $lastmod): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $this->latestUpdatedAt($lastmod),
        ];
    }

    private function latestUpdatedAt(mixed $value): ?CarbonInterface
    {
        if ($value instanceof CarbonInterface) {
            return $value;
        }

        if ($value instanceof Collection) {
            return $value
                ->map(fn ($item) => $this->latestUpdatedAt($item))
                ->filter()
                ->sortByDesc(fn (CarbonInterface $date) => $date->getTimestamp())
                ->first();
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => $this->latestUpdatedAt($item))
                ->filter()
                ->sortByDesc(fn (CarbonInterface $date) => $date->getTimestamp())
                ->first();
        }

        return data_get($value, 'updated_at') instanceof CarbonInterface
            ? data_get($value, 'updated_at')
            : null;
    }

    private function newCarYears(): Collection
    {
        return Car::query()
            ->whereHas('brand')
            ->where('is_soon', false)
            ->whereNotNull('year')
            ->pluck('year')
            ->filter(fn ($year): bool => is_string($year) && preg_match('/^20\d{2}$/', $year) === 1)
            ->unique()
            ->sortDesc()
            ->values();
    }
}
