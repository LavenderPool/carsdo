<?php

namespace App\Support\Seo;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarCatalog;
use App\Models\CarPageSeo;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarTestDrive;
use App\Models\City;
use App\Models\Page;
use App\Support\Articles\ArticleBodyRenderer;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class PageSeoFactory
{
    /**
     * @var array<string, array<string, string|null>>|null
     */
    private ?array $carPageOverridesCache = null;

    public function forView(string $viewName, array $data): ?SEOData
    {
        return $this->resolveForView($viewName, $data)?->seoData;
    }

    public function resolveForView(string $viewName, array $data): ?ResolvedPageSeo
    {
        return match ($viewName) {
            'site.main' => $this->forHome(
                $this->toCollection($data['newCars'] ?? null),
                $this->toCollection($data['soonCars'] ?? null),
                $this->toCollection($data['crashTests'] ?? null),
                $this->toCollection($data['testDrives'] ?? null),
                $this->toCollection($data['popularCars'] ?? null),
            ),
            'site.brand' => isset($data['brand'])
                ? $this->forBrand(
                    $data['brand'],
                    $this->toCollection($data['currentCars'] ?? null),
                    $this->toCollection($data['soonCars'] ?? null),
                    $this->toCollection($data['otherCars'] ?? null),
                    (int) ($data['currentYear'] ?? now()->year),
                )
                : null,
            'site.new-cars' => isset($data['year'], $data['newCars'])
                ? $this->forNewCars((string) $data['year'], $data['newCars'])
                : null,
            'site.electric-cars' => isset($data['electricCars'])
                ? $this->forElectricCars(
                    $data['electricCars'],
                    $this->toCollection($data['soonElectricCars'] ?? null),
                )
                : null,
            'site.catalog' => isset($data['catalog'], $data['cars'])
                ? $this->forCatalog($data['catalog'], $data['cars'])
                : null,
            'site.crash-trests' => $this->forCrashTests(
                $this->toCollection($data['crashTests'] ?? null),
                $data['selectedCrashTestBrand'] ?? null,
                (bool) ($data['isElectricOnly'] ?? false),
            ),
            'site.test-drives' => $this->forTestDrives(
                $this->toCollection($data['testDrives'] ?? null),
                $data['selectedTestDriveBrand'] ?? null,
                (bool) ($data['isElectricOnly'] ?? false),
            ),
            'site.cars-photo' => $this->forCarsPhoto(
                $this->toCollection($data['carsWithPhotos'] ?? null),
                $data['selectedPhotoBrand'] ?? null,
            ),
            'site.blog.index' => isset($data['articles'])
                ? $this->forBlogIndex($data['articles'])
                : null,
            'site.blog.show' => isset($data['article'])
                ? $this->forBlogShow($data['article'])
                : null,
            'site.pages.show' => isset($data['page'])
                ? $this->forPageShow($data['page'])
                : null,
            'site.car.index' => isset($data['brand'], $data['car'])
                ? $this->forCarIndex($data['brand'], $data['car'])
                : null,
            'site.car.equipment' => isset($data['brand'], $data['car'], $data['selectedConfiguration'], $data['selectedGroup'])
                ? $this->forCarEquipment($data['brand'], $data['car'], $data['selectedConfiguration'], $data['selectedGroup'])
                : null,
            'site.car.dealer' => isset($data['brand'], $data['car'], $data['city'])
                ? $this->forCarDealer($data['brand'], $data['car'], $data['city'])
                : null,
            'site.car.reviews' => isset($data['brand'], $data['car'])
                ? $this->forCarReviews($data['brand'], $data['car'])
                : null,
            'site.car.crash-test' => isset($data['brand'], $data['car'])
                ? $this->forCarCrashTest($data['brand'], $data['car'])
                : null,
            'site.car.test-drive' => isset($data['brand'], $data['car'])
                ? $this->forCarTestDrive($data['brand'], $data['car'])
                : null,
            'site.car.photo' => isset($data['brand'], $data['car'])
                ? $this->forCarPhoto($data['brand'], $data['car'])
                : null,
            default => null,
        };
    }

    private function forHome(
        Collection $newCars,
        Collection $soonCars,
        Collection $crashTests,
        Collection $testDrives,
        Collection $popularCars,
    ): ResolvedPageSeo {
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;
        $defaultTitle = "Новые автомобили в России {$currentYear}-{$nextYear}: цены, комплектации и фото";
        $defaultDescription = $this->limitDescription(
            "Каталог новых автомобилей в России {$currentYear}-{$nextYear}: цены и комплектации, фото, "
            ."тест-драйвы и краш-тесты. На сайте собрано {$newCars->count()} актуальных новинок и {$soonCars->count()} будущих моделей."
        );
        $defaultImage = $this->firstCarImage($newCars)
            ?? $this->firstCarImage($popularCars)
            ?? $this->fallbackImage();

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $defaultImage,
                'h1' => 'Новые автомобили в России',
            ],
            overrides: $this->sitePageOverrides('home'),
            context: [
                'site_name' => $this->siteName(),
                'current_year' => $currentYear,
                'next_year' => $nextYear,
                'new_cars_count' => $newCars->count(),
                'soon_cars_count' => $soonCars->count(),
                'crash_tests_count' => $crashTests->count(),
                'test_drives_count' => $testDrives->count(),
                'popular_cars_count' => $popularCars->count(),
                'page' => $this->currentPageNumber(),
            ],
            modifiedTime: $this->latestUpdatedAt([
                $newCars,
                $soonCars,
                $crashTests,
                $testDrives,
                $popularCars,
            ]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                ],
                array_filter([
                    $this->itemListSchema(
                        'Новые автомобили',
                        $newCars->map(fn (Car $car): array => $this->carListItem($car))->all(),
                    ),
                ]),
            ),
        );
    }

    private function forBrand(
        Brand $brand,
        Collection $currentCars,
        Collection $soonCars,
        Collection $otherCars,
        int $currentYear,
    ): ResolvedPageSeo {
        $defaultTitle = "{$brand->name} в России: модельный ряд {$currentYear}, цены и комплектации";
        $defaultDescription = $this->limitDescription(
            "Модельный ряд {$brand->name} в России: цены, комплектации, фото, обзоры и характеристики. "
            ."В каталоге {$currentCars->count()} актуальных моделей, {$soonCars->count()} ожидаемых новинок и {$otherCars->count()} дополнительных версий."
        );
        $defaultImage = $this->firstCarImage($currentCars)
            ?? $this->firstCarImage($soonCars)
            ?? $this->firstCarImage($otherCars)
            ?? $this->fallbackImage();

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $defaultImage,
                'h1' => "{$brand->name} › Модельный ряд",
            ],
            overrides: $this->modelSeoOverrides($brand),
            context: array_merge($this->baseContext(), [
                'brand' => $brand->name,
                'current_year' => $currentYear,
                'current_cars_count' => $currentCars->count(),
                'soon_cars_count' => $soonCars->count(),
                'other_cars_count' => $otherCars->count(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $currentCars, $soonCars, $otherCars]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                ],
                array_filter([
                    $this->itemListSchema(
                        "Модельный ряд {$brand->name}",
                        $currentCars
                            ->map(fn (Car $car): array => [
                                'name' => "{$brand->name} {$car->name}",
                                'url' => $this->carUrl($brand, $car),
                            ])
                            ->all(),
                    ),
                ]),
            ),
        );
    }

    private function forNewCars(string $year, LengthAwarePaginator $newCars): ResolvedPageSeo
    {
        $items = collect($newCars->items());
        $defaultTitle = $this->withPageNumber("Новые автомобили {$year} в России: цены, комплектации и фото");
        $defaultDescription = $this->limitDescription(
            "Новые автомобили {$year} года в России: каталог новинок с ценами, комплектациями, фото и характеристиками. "
            ."На странице {$items->count()} моделей из {$newCars->total()}."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->firstCarImage($items) ?? $this->fallbackImage(),
                'h1' => "Новые автомобили {$year}",
            ],
            overrides: $this->sitePageOverrides('new_cars'),
            context: array_merge($this->baseContext(), [
                'year' => $year,
                'items_count' => $items->count(),
                'total_count' => $newCars->total(),
                'page' => $newCars->currentPage(),
            ]),
            modifiedTime: $this->latestUpdatedAt($items),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => "Новые автомобили {$year}", 'url' => $this->absoluteUrl("/new-cars-{$year}/")],
                ],
                array_filter([
                    $this->itemListSchema(
                        "Новые автомобили {$year}",
                        $items->map(fn (Car $car): array => $this->carListItem($car))->all(),
                    ),
                ]),
            ),
        );
    }

    private function forElectricCars(LengthAwarePaginator $electricCars, Collection $soonElectricCars): ResolvedPageSeo
    {
        $currentYear = now()->year;
        $defaultTitle = $this->withPageNumber("Электромобили в России {$currentYear}: цены, комплектации и фото");
        $defaultDescription = $this->limitDescription(
            "Каталог новых электромобилей в России {$currentYear}: цены, комплектации, фото и характеристики. "
            ."Сейчас в продаже {$electricCars->total()} моделей, а {$soonElectricCars->count()} электрических новинок ожидаются в продаже."
        );
        $items = collect($electricCars->items());

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->firstCarImage($items)
                    ?? $this->firstCarImage($soonElectricCars)
                    ?? $this->fallbackImage(),
                'h1' => 'Электромобили в России',
            ],
            overrides: $this->sitePageOverrides('electric_cars'),
            context: array_merge($this->baseContext(), [
                'current_year' => $currentYear,
                'items_count' => $items->count(),
                'total_count' => $electricCars->total(),
                'soon_cars_count' => $soonElectricCars->count(),
                'page' => $electricCars->currentPage(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$items, $soonElectricCars]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => 'Электромобили', 'url' => $this->absoluteUrl('/electric-cars/')],
                ],
                array_filter([
                    $this->itemListSchema(
                        'Электромобили в России',
                        $items->map(fn (Car $car): array => $this->carListItem($car))->all(),
                    ),
                ]),
            ),
        );
    }

    private function forCatalog(CarCatalog $catalog, LengthAwarePaginator $cars): ResolvedPageSeo
    {
        $items = collect($cars->items());
        $defaultTitle = $this->withPageNumber("{$catalog->name}: каталог автомобилей");
        $defaultDescription = $this->limitDescription(
            "Подборка автомобилей {$catalog->name}. На странице {$items->count()} моделей из {$cars->total()}."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->firstCarImage($items) ?? $this->fallbackImage(),
                'h1' => $catalog->name,
            ],
            overrides: $this->modelSeoOverrides($catalog),
            context: array_merge($this->baseContext(), [
                'catalog' => $catalog->name,
                'items_count' => $items->count(),
                'total_count' => $cars->total(),
                'page' => $cars->currentPage(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$catalog, $items]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $catalog->name, 'url' => $this->absoluteUrl("/catalogs/{$catalog->slug}/")],
                ],
                array_filter([
                    $this->itemListSchema(
                        $catalog->name,
                        $items
                            ->map(fn (Car $car): array => $this->carListItem($car))
                            ->all(),
                    ),
                ]),
            ),
        );
    }

    private function forCrashTests(Collection $crashTests, ?Brand $brand, bool $isElectricOnly): ResolvedPageSeo
    {
        $baseTitle = match (true) {
            $brand !== null => "Краш-тесты {$brand->name}: рейтинг безопасности и видео",
            $isElectricOnly => 'Краш-тесты электромобилей: рейтинг безопасности и видео',
            default => 'Краш-тесты автомобилей: рейтинг безопасности и видео',
        };
        $defaultDescription = $this->limitDescription(
            $brand !== null
                ? "Краш-тесты {$brand->name}: видео, рейтинги безопасности и результаты независимых испытаний новых автомобилей."
                : ($isElectricOnly
                    ? 'Краш-тесты электромобилей: результаты независимых испытаний, рейтинг безопасности и видео новых моделей.'
                    : 'Краш-тесты новых автомобилей: независимая оценка безопасности, рейтинги и видео испытаний.')
        );
        $defaultTitle = $this->withPageNumber($baseTitle);
        $filterLabel = $brand !== null
            ? $brand->name
            : ($isElectricOnly ? 'Электромобили' : 'Последние');

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->firstCrashTestImage($crashTests) ?? $this->fallbackImage(),
                'h1' => $brand !== null ? "Краш-тесты {$brand->name}" : ($isElectricOnly ? 'Краш-тесты электромобилей' : 'Краш-тесты'),
            ],
            overrides: $this->sitePageOverrides('crash_tests'),
            context: array_merge($this->baseContext(), [
                'brand' => $brand?->name ?? '',
                'filter_label' => $filterLabel,
                'items_count' => $crashTests->count(),
                'page' => $this->currentPageNumber(),
            ]),
            modifiedTime: $this->latestUpdatedAt($crashTests),
            schema: $this->makeSchema(
                $this->crashTestBreadcrumbs($brand, $isElectricOnly),
                array_filter([
                    $this->itemListSchema(
                        $brand !== null ? "Краш-тесты {$brand->name}" : 'Краш-тесты автомобилей',
                        $crashTests
                            ->map(function (CarCrashTest $crashTest): ?array {
                                $car = $crashTest->car;
                                $brand = $car?->brand;

                                if (! $car || ! $brand) {
                                    return null;
                                }

                                return [
                                    'name' => "{$brand->name} {$car->name}",
                                    'url' => $this->absoluteUrl($this->carSubpagePath($brand, $car, 'crash-test')),
                                ];
                            })
                            ->filter()
                            ->values()
                            ->all(),
                    ),
                ]),
            ),
        );
    }

    private function forTestDrives(Collection $testDrives, ?Brand $brand, bool $isElectricOnly): ResolvedPageSeo
    {
        $baseTitle = match (true) {
            $brand !== null => "Тест-драйвы {$brand->name}: видео и обзоры новых автомобилей",
            $isElectricOnly => 'Тест-драйвы электромобилей: видео и обзоры новых моделей',
            default => 'Тест-драйвы автомобилей: видео и обзоры новых моделей',
        };
        $defaultDescription = $this->limitDescription(
            $brand !== null
                ? "Тест-драйвы {$brand->name}: подборка видео обзоров, впечатлений и тестов новых автомобилей."
                : ($isElectricOnly
                    ? 'Тест-драйвы электромобилей: подборка видео обзоров и испытаний новых электрических моделей.'
                    : 'Тест-драйвы новых автомобилей: видео обзоры, впечатления от вождения и подборка актуальных тестов.')
        );
        $defaultTitle = $this->withPageNumber($baseTitle);
        $filterLabel = $brand !== null
            ? $brand->name
            : ($isElectricOnly ? 'Электромобили' : 'Последние');

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->firstTestDriveImage($testDrives) ?? $this->fallbackImage(),
                'h1' => $brand !== null ? "Тест-драйвы {$brand->name}" : ($isElectricOnly ? 'Тест-драйвы электромобилей' : 'Тест-драйвы'),
            ],
            overrides: $this->sitePageOverrides('test_drives'),
            context: array_merge($this->baseContext(), [
                'brand' => $brand?->name ?? '',
                'filter_label' => $filterLabel,
                'items_count' => $testDrives->count(),
                'page' => $this->currentPageNumber(),
            ]),
            modifiedTime: $this->latestUpdatedAt($testDrives),
            schema: $this->makeSchema(
                $this->testDriveBreadcrumbs($brand, $isElectricOnly),
                array_filter([
                    $this->itemListSchema(
                        $brand !== null ? "Тест-драйвы {$brand->name}" : 'Тест-драйвы автомобилей',
                        $testDrives
                            ->map(function (CarTestDrive $testDrive): ?array {
                                $car = $testDrive->car;
                                $brand = $car?->brand;

                                if (! $car || ! $brand) {
                                    return null;
                                }

                                return [
                                    'name' => "{$brand->name} {$car->name}",
                                    'url' => $this->absoluteUrl($this->carSubpagePath($brand, $car, 'test-drive')),
                                ];
                            })
                            ->filter()
                            ->values()
                            ->all(),
                    ),
                ]),
            ),
        );
    }

    private function forCarsPhoto(Collection $carsWithPhotos, ?Brand $brand): ResolvedPageSeo
    {
        $defaultTitle = $this->withPageNumber(
            $brand !== null
                ? "Фото новых автомобилей {$brand->name}: каталог моделей"
                : 'Фото новых автомобилей: каталог моделей и галерея'
        );
        $defaultDescription = $this->limitDescription(
            $brand !== null
                ? "Фото новых автомобилей {$brand->name}: каталог моделей, цены, комплектации и галерея актуальных новинок."
                : 'Фото новых автомобилей в России: каталог моделей, галерея новинок, цены и комплектации.'
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->firstCarImage($carsWithPhotos) ?? $this->fallbackImage(),
                'h1' => $brand !== null ? "Фото новых автомобилей {$brand->name}" : 'Фото новых автомобилей',
            ],
            overrides: $this->sitePageOverrides('cars_photo'),
            context: array_merge($this->baseContext(), [
                'brand' => $brand?->name ?? '',
                'items_count' => $carsWithPhotos->count(),
                'page' => $this->currentPageNumber(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $carsWithPhotos]),
            schema: $this->makeSchema(
                array_values(array_filter([
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => 'Фото автомобилей', 'url' => $this->absoluteUrl('/cars-photo/')],
                    $brand !== null ? ['name' => $brand->name, 'url' => $this->absoluteUrl("/cars-photo/{$brand->slug}/")] : null,
                ])),
                array_filter([
                    $this->itemListSchema(
                        $brand !== null ? "Фото автомобилей {$brand->name}" : 'Фото новых автомобилей',
                        $carsWithPhotos
                            ->map(function (Car $car): ?array {
                                $brand = $car->brand;

                                if (! $brand) {
                                    return null;
                                }

                                return $this->carListItem($car);
                            })
                            ->filter()
                            ->values()
                            ->all(),
                    ),
                ]),
            ),
        );
    }

    private function forBlogIndex(LengthAwarePaginator $articles): ResolvedPageSeo
    {
        $items = collect($articles->items());
        $defaultTitle = $this->withPageNumber('Блог об автомобилях: новости, обзоры и аналитика');
        $defaultDescription = $this->limitDescription(
            "Блог CarsDo: новости автомобильного рынка, обзоры, аналитика и полезные материалы. "
            ."На странице {$items->count()} публикаций из {$articles->total()}."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $items->first()?->coverUrl() ?? $this->fallbackImage(),
                'h1' => 'Блог',
            ],
            overrides: $this->sitePageOverrides('blog'),
            context: array_merge($this->baseContext(), [
                'items_count' => $items->count(),
                'total_count' => $articles->total(),
                'page' => $articles->currentPage(),
            ]),
            modifiedTime: $this->latestUpdatedAt($items),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => 'Блог', 'url' => $this->absoluteUrl('/blog/')],
                ],
                array_filter([
                    $this->itemListSchema(
                        'Блог',
                        $items
                            ->map(fn (Article $article): array => [
                                'name' => $article->title,
                                'url' => $this->articleUrl($article),
                            ])
                            ->all(),
                    ),
                ]),
            ),
        );
    }

    private function forBlogShow(Article $article): ResolvedPageSeo
    {
        $defaultTitle = $article->title;
        $articleBodyText = $article->body_json !== null
            ? app(ArticleBodyRenderer::class)->toPlainText($article->body_json)
            : strip_tags((string) $article->body);
        $defaultDescription = $this->limitDescription(
            filled($article->excerpt)
                ? (string) $article->excerpt
                : $articleBodyText
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $article->coverUrl(),
                'h1' => $article->title,
            ],
            overrides: $this->modelSeoOverrides($article),
            context: array_merge($this->baseContext(), [
                'article' => $article->title,
                'published_at' => $article->published_at?->toDateString() ?? '',
            ]),
            modifiedTime: $this->latestUpdatedAt($article),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => 'Блог', 'url' => $this->absoluteUrl('/blog/')],
                    ['name' => $article->title, 'url' => $this->articleUrl($article)],
                ],
                array_filter([
                    $this->blogPostingSchema($article, $defaultDescription),
                ]),
            ),
        );
    }

    private function forPageShow(Page $page): ResolvedPageSeo
    {
        $defaultTitle = $page->title;
        $defaultDescription = $this->limitDescription(
            filled($page->excerpt)
                ? (string) $page->excerpt
                : ($page->body_json !== null
                    ? app(ArticleBodyRenderer::class)->toPlainText($page->body_json)
                    : strip_tags((string) $page->body))
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $this->fallbackImage(),
                'h1' => $page->title,
            ],
            overrides: $this->modelSeoOverrides($page),
            context: array_merge($this->baseContext(), [
                'content_page' => $page->title,
                'published_at' => $page->published_at?->toDateString() ?? '',
            ]),
            modifiedTime: $this->latestUpdatedAt($page),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $page->title, 'url' => $this->pageUrl($page)],
                ],
                array_filter([
                    $this->webPageSchema($page, $defaultDescription),
                ]),
            ),
        );
    }

    private function forCarIndex(Brand $brand, Car $car): ResolvedPageSeo
    {
        $configurations = $this->toCollection($car->configurations);
        $configurationGroups = $this->toCollection($car->configurationGroups);
        $priceText = $this->carPriceText($car, $configurations);
        $defaultTitle = "{$brand->name} {$car->name}: цены и комплектации в России, фото и характеристики";
        $defaultDescription = $this->limitDescription(
            "{$brand->name} {$car->name}: цены и комплектации в России, фото, характеристики, тест-драйвы и краш-тесты. "
            ."Актуальная цена: {$priceText}. Доступно {$configurationGroups->count()} комплектаций."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(false),
                'h1' => $car->name,
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car),
                $this->carPageOverrides('car_index'),
            ),
            context: array_merge($this->carContext($brand, $car), [
                'price' => $priceText,
                'price_range' => $priceText,
                'configurations_count' => $configurationGroups->count(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $configurations, $configurationGroups]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                ],
                array_filter([
                    $this->carProductSchema($brand, $car, $configurations, generateIfMissing: false),
                ]),
            ),
        );
    }

    private function forCarEquipment(
        Brand $brand,
        Car $car,
        CarConfiguration $selectedConfiguration,
        CarConfigurationGroup $selectedGroup,
    ): ResolvedPageSeo
    {
        $configurations = $this->toCollection($car->configurations)
            ->sortBy([
                ['car_configuration_group_id', 'asc'],
                ['import_index', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
        $defaultTitle = "{$brand->name} {$car->name} {$selectedGroup->name}: цена, характеристики и оборудование";
        $defaultDescription = $this->limitDescription(
            "{$brand->name} {$car->name} в комплектации {$selectedGroup->name}: оборудование, опции, характеристики и цена "
            .$this->configurationPriceText($selectedConfiguration).'.'
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(),
                'h1' => "{$car->name} › {$selectedGroup->name}",
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car, 'equipment_seo'),
                $this->carPageOverrides('car_equipment'),
            ),
            context: array_merge($this->carContext($brand, $car), [
                'group' => $selectedGroup->name,
                'configuration_price' => $this->configurationPriceText($selectedConfiguration),
                'configuration_local_id' => (string) ($selectedConfiguration->local_id ?? ''),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $selectedGroup, $selectedConfiguration, $configurations]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                    ['name' => $selectedGroup->name, 'url' => $this->canonicalUrl()],
                ],
                array_filter([
                    $this->carProductSchema($brand, $car, $configurations, $selectedConfiguration, $selectedGroup),
                ]),
            ),
        );
    }

    private function forCarReviews(Brand $brand, Car $car): ResolvedPageSeo
    {
        $reviews = $this->toCollection($car->reviews);
        $defaultTitle = "{$brand->name} {$car->name}: отзывы владельцев, плюсы и минусы";
        $defaultDescription = $this->limitDescription(
            "Отзывы владельцев {$brand->name} {$car->name}: преимущества, недостатки и впечатления от эксплуатации. "
            ."На странице собрано {$reviews->count()} отзывов и заметок."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(),
                'h1' => "{$car->name} › Отзывы владельцев (плюсы и минусы)",
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car, 'reviews_seo'),
                $this->carPageOverrides('car_reviews'),
            ),
            context: array_merge($this->carContext($brand, $car), [
                'reviews_count' => $reviews->count(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $reviews]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                    ['name' => 'Отзывы', 'url' => $this->canonicalUrl()],
                ],
            ),
        );
    }

    private function forCarDealer(Brand $brand, Car $car, City $city): ResolvedPageSeo
    {
        $defaultTitle = "{$car->name}- Официальные дилеры";
        $cityInPrepositional = $city->nameInPrepositionalCase();
        $currentYear = now()->year;
        $defaultDescription = $this->limitDescription(
            "Новый список официальных дилеров {$car->name} в {$cityInPrepositional} {$currentYear}. "
            ."Официальные комплектации и цены (от производителя автомобиля) в автосалонах, фото новой модели."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(),
                'h1' => "{$car->name} › Официальные дилеры ({$city->name})",
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car, 'dealer_seo'),
                $this->carPageOverrides('car_dealer'),
            ),
            context: array_merge($this->carContext($brand, $car), [
                'city' => $city->name,
                'current_year' => now()->year,
                'price' => $this->carPriceText($car, $this->toCollection($car->configurations)),
                'price_range' => $this->carPriceText($car, $this->toCollection($car->configurations)),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $city]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                    ['name' => "Официальные дилеры ({$city->name})", 'url' => $this->canonicalUrl()],
                ],
                array_filter([
                    $this->carProductSchema($brand, $car, $this->toCollection($car->configurations)),
                ]),
            ),
        );
    }

    private function forCarCrashTest(Brand $brand, Car $car): ResolvedPageSeo
    {
        $crashTest = $car->crashTest;
        $ratingText = filled($crashTest?->rating) ? ", результат {$crashTest->rating} из 5" : '';
        $yearText = filled($crashTest?->year) ? " {$crashTest->year} года" : '';
        $defaultTitle = "{$brand->name} {$car->name}: краш-тест, рейтинг безопасности и видео";
        $defaultDescription = $this->limitDescription(
            "{$brand->name} {$car->name}: краш-тест{$yearText}, видео независимой оценки безопасности{$ratingText}."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(),
                'h1' => "{$car->name} › Краш-тест",
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car, 'crash_test_seo'),
                $this->carPageOverrides('car_crash_test'),
            ),
            context: array_merge($this->carContext($brand, $car), [
                'crash_test_year' => (string) ($crashTest?->year ?? ''),
                'crash_test_rating' => (string) ($crashTest?->rating ?? ''),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $crashTest]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                    ['name' => 'Краш-тест', 'url' => $this->canonicalUrl()],
                ],
                array_filter([
                    $this->carProductSchema($brand, $car, $this->toCollection($car->configurations)),
                    $this->videoSchema(
                        name: "Краш-тест {$brand->name} {$car->name}",
                        description: $defaultDescription,
                        youtubeId: $this->extractYoutubeId($crashTest?->video_path),
                        image: $car->coverUrl(),
                        modifiedTime: $this->coerceDate($crashTest?->updated_at) ?? $this->coerceDate($car->updated_at),
                    ),
                ]),
            ),
        );
    }

    private function forCarTestDrive(Brand $brand, Car $car): ResolvedPageSeo
    {
        $testDrives = $this->toCollection($car->testDrives)
            ->sortBy([
                ['import_index', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
        $defaultTitle = "{$brand->name} {$car->name}: тест-драйв, видео и обзор";
        $defaultDescription = $this->limitDescription(
            "Тест-драйв {$brand->name} {$car->name}: видео обзоры, впечатления от вождения и подборка тестов. "
            ."На странице {$testDrives->count()} материалов."
        );
        $firstVideo = $testDrives->first();

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(),
                'h1' => "{$car->name} › Тест-драйв",
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car, 'test_drive_seo'),
                $this->carPageOverrides('car_test_drive'),
            ),
            context: array_merge($this->carContext($brand, $car), [
                'test_drives_count' => $testDrives->count(),
            ]),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $testDrives]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                    ['name' => 'Тест-драйв', 'url' => $this->canonicalUrl()],
                ],
                array_filter([
                    $this->carProductSchema($brand, $car, $this->toCollection($car->configurations)),
                    $this->videoSchema(
                        name: "Тест-драйв {$brand->name} {$car->name}",
                        description: $defaultDescription,
                        youtubeId: $this->extractYoutubeId($firstVideo?->video_path),
                        image: $car->coverUrl(),
                        modifiedTime: $this->coerceDate($firstVideo?->updated_at) ?? $this->coerceDate($car->updated_at),
                    ),
                ]),
            ),
        );
    }

    private function forCarPhoto(Brand $brand, Car $car): ResolvedPageSeo
    {
        $defaultTitle = "{$car->name} - фото салона, новый кузов";
        $defaultDescription = $this->limitDescription(
            "{$car->name} - фото нового кузова, фото внутри салона автомобиля (экстерьер и интерьер) новой модели."
        );

        return $this->buildResolvedSeo(
            defaults: [
                'title' => $defaultTitle,
                'description' => $defaultDescription,
                'image' => $car->coverUrl(),
                'h1' => "{$car->name} › Фото",
            ],
            overrides: $this->layeredOverrides(
                $this->modelSeoOverrides($car, 'photo_seo'),
                $this->carPageOverrides('car_photo'),
            ),
            context: $this->carContext($brand, $car),
            modifiedTime: $this->latestUpdatedAt([$brand, $car, $this->toCollection($car->photos), $this->toCollection($car->photoGroups)]),
            schema: $this->makeSchema(
                [
                    ['name' => 'Главная', 'url' => $this->homeUrl()],
                    ['name' => $brand->name, 'url' => $this->brandUrl($brand)],
                    ['name' => "{$brand->name} {$car->name}", 'url' => $this->carUrl($brand, $car)],
                    ['name' => 'Фото', 'url' => $this->canonicalUrl()],
                ],
                array_filter([
                    $this->carProductSchema($brand, $car, $this->toCollection($car->configurations)),
                ]),
            ),
        );
    }

    private function buildResolvedSeo(
        array $defaults,
        array $overrides,
        array $context,
        ?CarbonInterface $modifiedTime,
        ?SchemaCollection $schema = null,
    ): ResolvedPageSeo {
        $title = $this->resolveOverrideValue($overrides['title'] ?? null, $context) ?? $defaults['title'];
        $description = $this->resolveOverrideValue($overrides['description'] ?? null, $context) ?? $defaults['description'];
        $h1 = $this->resolveOverrideValue($overrides['h1'] ?? null, $context) ?? ($defaults['h1'] ?? null);
        $image = $this->resolveImageOverride($overrides['og_image'] ?? null, $context) ?? $defaults['image'];
        $canonicalUrl = $this->resolveCanonicalOverride($overrides['canonical_url'] ?? null, $context) ?? $this->canonicalUrl();
        $robots = $this->resolveOverrideValue($overrides['robots'] ?? null, $context) ?? $this->defaultRobots();

        return new ResolvedPageSeo(
            seoData: $this->makeSeoData(
                title: $title,
                description: $description,
                image: $image,
                canonicalUrl: $canonicalUrl,
                robots: $robots,
                modifiedTime: $modifiedTime,
                schema: $schema,
            ),
            h1: $h1,
        );
    }

    private function makeSeoData(
        string $title,
        string $description,
        string $image,
        string $canonicalUrl,
        string $robots,
        ?CarbonInterface $modifiedTime,
        ?SchemaCollection $schema = null,
    ): SEOData {
        return new SEOData(
            title: $title,
            description: $description,
            image: $this->absoluteUrl($image),
            url: $canonicalUrl,
            modified_time: $modifiedTime,
            schema: $schema,
            site_name: $this->siteName(),
            canonical_url: $canonicalUrl,
            robots: $robots,
        );
    }

    private function makeSchema(array $breadcrumbs, array $extraSchemas = []): SchemaCollection
    {
        $schema = SchemaCollection::initialize();
        $schema->push($this->websiteSchema());
        $schema->push($this->organizationSchema());

        if ($breadcrumbs !== []) {
            $schema->push($this->breadcrumbSchema($breadcrumbs));
        }

        foreach ($extraSchemas as $extraSchema) {
            if (is_array($extraSchema) && $extraSchema !== []) {
                $schema->push($extraSchema);
            }
        }

        return $schema;
    }

    private function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $this->siteName(),
            'url' => $this->homeUrl(),
            'inLanguage' => app()->getLocale(),
        ];
    }

    private function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->siteName(),
            'url' => $this->homeUrl(),
            'logo' => $this->absoluteUrl('/assets/img/logo.png'),
        ];
    }

    private function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)
                ->values()
                ->map(fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ])
                ->all(),
        ];
    }

    private function itemListSchema(string $name, array $items): ?array
    {
        if ($items === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'itemListElement' => collect($items)
                ->values()
                ->map(fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'url' => $item['url'],
                ])
                ->all(),
        ];
    }

    private function carProductSchema(
        Brand $brand,
        Car $car,
        Collection $configurations,
        mixed $selectedConfiguration = null,
        ?CarConfigurationGroup $selectedGroup = null,
        bool $generateIfMissing = true,
    ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'additionalType' => 'https://schema.org/Car',
            'name' => "{$brand->name} {$car->name}",
            'url' => $selectedGroup !== null ? $this->canonicalUrl() : $this->carUrl($brand, $car),
            'image' => $this->absoluteUrl($car->coverUrl($generateIfMissing)),
            'brand' => [
                '@type' => 'Brand',
                'name' => $brand->name,
            ],
            'model' => $car->name,
            'category' => 'Автомобили',
            'description' => $selectedGroup !== null
                ? "{$brand->name} {$car->name} в комплектации {$selectedGroup->name}"
                : "{$brand->name} {$car->name}",
        ];

        if (filled($car->year)) {
            $schema['releaseDate'] = (string) $car->year;
        }

        if (filled($car->official_site)) {
            $schema['sameAs'] = [$car->official_site];
        }

        $priceOffer = $this->priceSchema($car, $configurations, $selectedConfiguration);
        if ($priceOffer !== null) {
            $schema['offers'] = $priceOffer;
        }

        $additionalProperty = $this->configurationProperties($selectedConfiguration ?: $configurations->first());
        if ($additionalProperty !== []) {
            $schema['additionalProperty'] = $additionalProperty;
        }

        return $schema;
    }

    private function priceSchema(Car $car, Collection $configurations, mixed $selectedConfiguration = null): ?array
    {
        if ($selectedConfiguration instanceof CarConfiguration && filled($selectedConfiguration->price)) {
            return [
                '@type' => 'Offer',
                'priceCurrency' => $this->currencyCode($selectedConfiguration->currency),
                'price' => (int) $selectedConfiguration->price,
                'availability' => $car->is_soon ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
                'url' => $this->canonicalUrl(),
            ];
        }

        $pricedConfigurations = $configurations
            ->filter(fn ($configuration): bool => filled($configuration->price ?? null))
            ->values();

        $prices = $pricedConfigurations
            ->map(fn ($configuration): int => (int) $configuration->price)
            ->values();

        if ($prices->isNotEmpty()) {
            $lowPrice = (int) $prices->min();
            $highPrice = (int) $prices->max();
            $currencyCode = $this->configurationsCurrencyCode($pricedConfigurations);

            if ($lowPrice === $highPrice) {
                return [
                    '@type' => 'Offer',
                    'priceCurrency' => $currencyCode,
                    'price' => $lowPrice,
                    'availability' => $car->is_soon ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
                    'url' => $this->canonicalUrl(),
                ];
            }

            return [
                '@type' => 'AggregateOffer',
                'priceCurrency' => $currencyCode,
                'lowPrice' => $lowPrice,
                'highPrice' => $highPrice,
                'offerCount' => $prices->count(),
                'availability' => $car->is_soon ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
                'url' => $this->canonicalUrl(),
            ];
        }

        if (filled($car->start_price) || filled($car->end_price)) {
            $lowPrice = filled($car->start_price) ? (int) $car->start_price : (int) $car->end_price;
            $highPrice = filled($car->end_price) ? (int) $car->end_price : $lowPrice;

            if ($lowPrice === $highPrice) {
                return [
                    '@type' => 'Offer',
                    'priceCurrency' => 'RUB',
                    'price' => $lowPrice,
                    'availability' => $car->is_soon ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
                    'url' => $this->canonicalUrl(),
                ];
            }

            return [
                '@type' => 'AggregateOffer',
                'priceCurrency' => 'RUB',
                'lowPrice' => $lowPrice,
                'highPrice' => $highPrice,
                'offerCount' => 1,
                'availability' => $car->is_soon ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
                'url' => $this->canonicalUrl(),
            ];
        }

        return null;
    }

    private function configurationsCurrencyCode(Collection $configurations): string
    {
        $currencies = $configurations
            ->pluck('currency')
            ->filter(fn ($currency): bool => filled($currency))
            ->unique()
            ->values();

        if ($currencies->count() === 1) {
            return $this->currencyCode($currencies->first());
        }

        return 'RUB';
    }

    private function currencyCode(?string $currency): string
    {
        return match (trim((string) $currency)) {
            '$', 'USD' => 'USD',
            default => 'RUB',
        };
    }

    private function configurationProperties(mixed $configuration): array
    {
        if (! $configuration instanceof CarConfiguration) {
            return [];
        }

        $properties = [];

        $map = [
            'Тип двигателя' => $configuration->engine_type,
            'Объем двигателя' => filled($configuration->engine_capacity) ? rtrim(rtrim((string) $configuration->engine_capacity, '0'), '.').' л' : null,
            'Мощность' => filled($configuration->horsepower) ? $configuration->horsepower.' л.с.' : null,
            'Коробка передач' => $configuration->transmission,
            'Привод' => $configuration->drive_type,
            'Расход в городе' => $configuration->fuel_city,
            'Расход по трассе' => $configuration->fuel_highway,
            'Расход смешанный' => $configuration->fuel_combined,
            'Разгон до 100 км/ч' => $configuration->acceleration,
            'Максимальная скорость' => $configuration->speed,
        ];

        foreach ($map as $name => $value) {
            if (! filled($value)) {
                continue;
            }

            $properties[] = [
                '@type' => 'PropertyValue',
                'name' => $name,
                'value' => (string) $value,
            ];
        }

        return $properties;
    }

    private function videoSchema(
        string $name,
        string $description,
        ?string $youtubeId,
        string $image,
        ?CarbonInterface $modifiedTime,
    ): ?array {
        if (! filled($youtubeId)) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $name,
            'description' => $description,
            'thumbnailUrl' => ["https://i.ytimg.com/vi/{$youtubeId}/hqdefault.jpg"],
            'embedUrl' => "https://www.youtube.com/embed/{$youtubeId}",
            'url' => $this->canonicalUrl(),
            'image' => $this->absoluteUrl($image),
        ];

        if ($modifiedTime !== null) {
            $schema['uploadDate'] = $modifiedTime->toAtomString();
        }

        return $schema;
    }

    private function blogPostingSchema(Article $article, string $description): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $article->title,
            'description' => $description,
            'url' => $this->articleUrl($article),
            'mainEntityOfPage' => $this->articleUrl($article),
            'image' => [$this->absoluteUrl($article->coverUrl())],
            'author' => [
                '@type' => 'Organization',
                'name' => $this->siteName(),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $this->siteName(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->absoluteUrl('/assets/img/logo.png'),
                ],
            ],
        ];

        if ($article->published_at !== null) {
            $schema['datePublished'] = $article->published_at->toAtomString();
        }

        if ($article->updated_at !== null) {
            $schema['dateModified'] = $article->updated_at->toAtomString();
        }

        return $schema;
    }

    private function webPageSchema(Page $page, string $description): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page->title,
            'description' => $description,
            'url' => $this->pageUrl($page),
        ];

        if ($page->published_at !== null) {
            $schema['datePublished'] = $page->published_at->toAtomString();
        }

        if ($page->updated_at !== null) {
            $schema['dateModified'] = $page->updated_at->toAtomString();
        }

        return $schema;
    }

    private function crashTestBreadcrumbs(?Brand $brand, bool $isElectricOnly): array
    {
        return array_values(array_filter([
            ['name' => 'Главная', 'url' => $this->homeUrl()],
            ['name' => 'Краш-тесты', 'url' => $this->absoluteUrl('/crash-test/')],
            $isElectricOnly ? ['name' => 'Электромобили', 'url' => $this->absoluteUrl('/crash-test/electric-cars/')] : null,
            $brand !== null ? ['name' => $brand->name, 'url' => $this->absoluteUrl("/crash-test/{$brand->slug}/")] : null,
        ]));
    }

    private function testDriveBreadcrumbs(?Brand $brand, bool $isElectricOnly): array
    {
        return array_values(array_filter([
            ['name' => 'Главная', 'url' => $this->homeUrl()],
            ['name' => 'Тест-драйвы', 'url' => $this->absoluteUrl('/test-drive/')],
            $isElectricOnly ? ['name' => 'Электромобили', 'url' => $this->absoluteUrl('/test-drive/electric-cars/')] : null,
            $brand !== null ? ['name' => $brand->name, 'url' => $this->absoluteUrl("/test-drive/{$brand->slug}/")] : null,
        ]));
    }

    private function carListItem(Car $car): array
    {
        return [
            'name' => trim(($car->brand?->name ? $car->brand->name.' ' : '').$car->name),
            'url' => $this->carUrl($car->brand, $car),
        ];
    }

    private function brandUrl(?Brand $brand): string
    {
        return $brand ? $this->absoluteUrl("/{$brand->slug}/") : $this->homeUrl();
    }

    private function carUrl(?Brand $brand, Car $car): string
    {
        if (! $brand) {
            return $this->homeUrl();
        }

        return $this->absoluteUrl($this->carPath($brand, $car));
    }

    private function articleUrl(Article $article): string
    {
        return $this->absoluteUrl("/blog/{$article->slug}/");
    }

    private function pageUrl(Page $page): string
    {
        return match ($page->slug) {
            'privacy-policy' => $this->absoluteUrl('/privacy-policy/'),
            'cookie-policy' => $this->absoluteUrl('/cookie-policy/'),
            'contacts' => $this->absoluteUrl('/contacts/'),
            default => $this->absoluteUrl("/pages/{$page->slug}/"),
        };
    }

    private function carPath(Brand $brand, Car $car): string
    {
        return "/{$brand->slug}/{$car->slug}/";
    }

    private function carSubpagePath(Brand $brand, Car $car, string $suffix): string
    {
        return "/{$brand->slug}/{$car->slug}/{$suffix}/";
    }

    private function canonicalUrl(): string
    {
        $path = request()->getPathInfo();
        $path = $path === '' ? '/' : $path;

        if ($path !== '/' && ! str_contains(basename($path), '.') && ! str_ends_with($path, '/')) {
            $path .= '/';
        }

        $query = request()->query();

        if (($query['page'] ?? null) === '1' || ($query['page'] ?? null) === 1) {
            unset($query['page']);
        }

        ksort($query);

        $url = $this->absoluteUrl($path);

        if ($query === []) {
            return $url;
        }

        return $url.'?'.http_build_query($query);
    }

    private function homeUrl(): string
    {
        return $this->absoluteUrl('/');
    }

    private function absoluteUrl(string $path): string
    {
        if (preg_match('~^https?://~', $path) === 1) {
            return $path;
        }

        $baseUrl = rtrim(url('/'), '/');

        if ($path === '/' || $path === '') {
            return $baseUrl.'/';
        }

        return $baseUrl.(str_starts_with($path, '/') ? $path : '/'.$path);
    }

    private function siteName(): string
    {
        return (string) (config('seo.site_name') ?: config('app.name', 'carsDo'));
    }

    private function fallbackImage(): string
    {
        return (string) (config('seo.image.fallback') ?: '/assets/img/start.png');
    }

    private function defaultRobots(): string
    {
        return (string) (
            config('seo.admin.default_robots')
            ?: 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'
        );
    }

    private function baseContext(): array
    {
        return [
            'site_name' => $this->siteName(),
            'current_year' => now()->year,
            'next_year' => now()->year + 1,
            'page' => $this->currentPageNumber(),
        ];
    }

    private function carContext(Brand $brand, Car $car): array
    {
        return array_merge($this->baseContext(), [
            'brand' => $brand->name,
            'car' => $car->name,
            'year' => (string) ($car->year ?? ''),
        ]);
    }

    private function currentPageNumber(): int
    {
        return max(1, (int) request()->integer('page'));
    }

    private function sitePageOverrides(string $prefix): array
    {
        return (array) config("seo.admin.pages.{$prefix}", []);
    }

    private function carPageOverrides(string $pageKey): array
    {
        if ($this->carPageOverridesCache === null) {
            $this->carPageOverridesCache = $this->loadCarPageOverrides();
        }

        return $this->carPageOverridesCache[$pageKey] ?? [];
    }

    private function modelSeoOverrides(object $model, string $prefix = 'seo'): array
    {
        return [
            'title' => data_get($model, "{$prefix}_title"),
            'description' => data_get($model, "{$prefix}_description"),
            'h1' => data_get($model, "{$prefix}_h1"),
            'og_image' => data_get($model, "{$prefix}_og_image"),
            'canonical_url' => data_get($model, "{$prefix}_canonical_url"),
            'robots' => data_get($model, "{$prefix}_robots"),
        ];
    }

    private function layeredOverrides(array ...$layers): array
    {
        $resolved = [];

        foreach (AdminSeoFields::BASE_KEYS as $field) {
            $resolved[$field] = null;

            foreach ($layers as $layer) {
                $candidate = $layer[$field] ?? null;

                if ($this->hasOverrideValue($candidate)) {
                    $resolved[$field] = $candidate;
                    break;
                }
            }
        }

        return $resolved;
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    private function loadCarPageOverrides(): array
    {
        if (! Schema::hasTable('car_page_seos')) {
            return [];
        }

        try {
            return CarPageSeo::query()
                ->get()
                ->mapWithKeys(fn (CarPageSeo $page): array => [
                    $page->page_key => $page->only(AdminSeoFields::BASE_KEYS),
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function hasOverrideValue(mixed $value): bool
    {
        if (! is_string($value)) {
            return $value !== null;
        }

        return trim($value) !== '';
    }

    private function resolveOverrideValue(mixed $value, array $context): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return $this->normalizeRenderedValue(strtr($value, $this->templateReplacements($context)));
    }

    private function resolveCanonicalOverride(mixed $value, array $context): ?string
    {
        $resolved = $this->resolveOverrideValue($value, $context);

        return $resolved !== null ? $this->absoluteUrl($resolved) : null;
    }

    private function resolveImageOverride(mixed $value, array $context): ?string
    {
        $resolved = $this->resolveOverrideValue($value, $context);

        return $resolved !== null ? $this->absoluteUrl($resolved) : null;
    }

    private function templateReplacements(array $context): array
    {
        $replacements = [];

        foreach ($context as $key => $value) {
            $replacements['{'.$key.'}'] = $this->stringifyContextValue($value);
        }

        return $replacements;
    }

    private function stringifyContextValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }

    private function normalizeRenderedValue(string $value): ?string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim($value)) ?: trim($value);
        $normalized = preg_replace('/\s+([,.:;!?])/u', '$1', $normalized) ?: $normalized;
        $normalized = preg_replace('/([:;,])(?=[^\s])/u', '$1 ', $normalized) ?: $normalized;
        $normalized = preg_replace('/\(\s+/u', '(', $normalized) ?: $normalized;
        $normalized = preg_replace('/\s+\)/u', ')', $normalized) ?: $normalized;

        return $normalized !== '' ? $normalized : null;
    }

    private function firstCarImage(Collection $cars): ?string
    {
        return $cars
            ->first(fn ($car) => $car instanceof Car && filled($car->coverUrl()))
            ?->coverUrl();
    }

    private function firstCrashTestImage(Collection $crashTests): ?string
    {
        /** @var CarCrashTest|null $crashTest */
        $crashTest = $crashTests->first(fn ($item) => $item instanceof CarCrashTest && $item->car?->brand !== null);

        return $crashTest?->car?->coverUrl();
    }

    private function firstTestDriveImage(Collection $testDrives): ?string
    {
        /** @var CarTestDrive|null $testDrive */
        $testDrive = $testDrives->first(fn ($item) => $item instanceof CarTestDrive && $item->car?->brand !== null);

        return $testDrive?->car?->coverUrl();
    }

    private function latestUpdatedAt(mixed $source): ?CarbonInterface
    {
        if ($source instanceof CarbonInterface) {
            return $source;
        }

        if ($source instanceof Collection) {
            return $source
                ->map(fn ($item) => $this->latestUpdatedAt($item))
                ->filter()
                ->sortByDesc(fn (CarbonInterface $date) => $date->getTimestamp())
                ->first();
        }

        if (is_array($source)) {
            return collect($source)
                ->map(fn ($item) => $this->latestUpdatedAt($item))
                ->filter()
                ->sortByDesc(fn (CarbonInterface $date) => $date->getTimestamp())
                ->first();
        }

        return $this->coerceDate(data_get($source, 'updated_at'));
    }

    private function coerceDate(mixed $value): ?CarbonInterface
    {
        return $value instanceof CarbonInterface ? $value : null;
    }

    private function toCollection(mixed $items): Collection
    {
        if ($items instanceof Collection) {
            return $items;
        }

        if ($items instanceof LengthAwarePaginator) {
            return collect($items->items());
        }

        return collect($items ?? []);
    }

    private function carPriceText(Car $car, Collection $configurations): string
    {
        $prices = $configurations
            ->pluck('price')
            ->filter(fn ($price): bool => filled($price))
            ->map(fn ($price): int => (int) $price)
            ->values();

        if ($prices->isNotEmpty()) {
            $minPrice = (int) $prices->min();
            $maxPrice = (int) $prices->max();

            return $minPrice === $maxPrice
                ? $this->formatPrice($minPrice)
                : $this->formatPrice($minPrice).' - '.$this->formatPrice($maxPrice);
        }

        if (filled($car->start_price) && filled($car->end_price)) {
            return (int) $car->start_price === (int) $car->end_price
                ? $this->formatPrice((int) $car->start_price)
                : $this->formatPrice((int) $car->start_price).' - '.$this->formatPrice((int) $car->end_price);
        }

        if (filled($car->start_price)) {
            return $this->formatPrice((int) $car->start_price);
        }

        if (filled($car->end_price)) {
            return $this->formatPrice((int) $car->end_price);
        }

        return 'не объявлена';
    }

    private function configurationPriceText(mixed $configuration): string
    {
        if ($configuration instanceof CarConfiguration && filled($configuration->price)) {
            return $this->formatPrice((int) $configuration->price).' руб.';
        }

        return 'не объявлена';
    }

    private function formatPrice(int $price): string
    {
        return number_format($price, 0, ',', ' ');
    }

    private function limitDescription(string $text): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim($text)) ?: trim($text);

        if (mb_strlen($normalized) <= 180) {
            return $normalized;
        }

        return rtrim(mb_substr($normalized, 0, 177)).'...';
    }

    private function withPageNumber(string $title): string
    {
        $page = max(1, (int) request()->integer('page'));

        return $page > 1 ? "{$title}, страница {$page}" : $title;
    }

    private function extractYoutubeId(?string $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $value) === 1) {
            return $value;
        }

        $parts = parse_url($value);
        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');

        if (str_contains($host, 'youtu.be')) {
            $candidate = trim($path, '/');

            return preg_match('~^[A-Za-z0-9_-]{11}$~', $candidate) === 1 ? $candidate : null;
        }

        if (str_contains($host, 'youtube.com')) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            $candidate = (string) ($query['v'] ?? '');

            if (preg_match('~^[A-Za-z0-9_-]{11}$~', $candidate) === 1) {
                return $candidate;
            }

            if (str_starts_with($path, '/embed/')) {
                $candidate = trim(substr($path, strlen('/embed/')), '/');

                return preg_match('~^[A-Za-z0-9_-]{11}$~', $candidate) === 1 ? $candidate : null;
            }
        }

        return null;
    }
}
