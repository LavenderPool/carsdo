<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Setting;
use App\Support\Seo\AdminSeoFields;
use App\Support\Seo\PageSeoFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $hasSettingsTable = Schema::hasTable('settings');
        $hasBrandsTable = Schema::hasTable('brands');
        $setting = $hasSettingsTable
            ? Setting::query()->firstOrCreate(
                ['id' => 1],
                ['brand_name' => 'carsDo'],
            )
            : new Setting(['brand_name' => 'carsDo']);
        $brands = $hasBrandsTable
            ? Brand::query()
                ->select(['name', 'slug', 'leave_from_russian'])
                ->orderBy('name')
                ->get()
            : collect();

        $siteBrandName = $setting->brand_name ?: 'carsDo';
        $siteFaviconPath = filled($setting->favicon_path)
            ? '/storage/'.ltrim((string) $setting->favicon_path, '/')
            : '/favicon.ico';
        $currentYear = now()->year;

        config([
            'seo.site_name' => $siteBrandName,
            'seo.sitemap' => '/sitemap.xml',
            'seo.favicon' => ltrim($siteFaviconPath, '/'),
            'seo.title.suffix' => $setting->seo_title_suffix ?: '',
            'seo.title.homepage_title' => null,
            'seo.description.fallback' => $setting->seo_default_description
                ?: 'Новые автомобили в России: цены, комплектации, фото, тест-драйвы и краш-тесты.',
            'seo.image.fallback' => $setting->seo_default_og_image ?: 'assets/img/start.png',
            'seo.admin.default_robots' => $setting->seo_default_robots,
            'seo.admin.pages' => $this->seoPageConfig($setting),
        ]);

        View::share([
            'siteBrandName' => $siteBrandName,
            'siteFaviconUrl' => asset(ltrim($siteFaviconPath, '/')),
            'footerBrandsActive' => $brands->where('leave_from_russian', false)->values(),
            'footerBrandsLeft' => $brands->where('leave_from_russian', true)->values(),
            'catalogYear' => $currentYear,
            'catalogPrevYear' => $currentYear - 1,
            'catalogPrevTwoYear' => $currentYear - 2,
        ]);

        View::composer('site.*', static function ($view): void {
            $resolvedSeo = app(PageSeoFactory::class)->resolveForView($view->name(), $view->getData());

            if ($resolvedSeo !== null) {
                $view->with('SEOData', $resolvedSeo->seoData);
                $view->with('pageH1', $resolvedSeo->h1);
            }
        });
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    private function seoPageConfig(Setting $setting): array
    {
        $pages = [];

        foreach (AdminSeoFields::SITE_PAGE_PREFIXES as $prefix) {
            $pages[$prefix] = [
                'title' => data_get($setting, "{$prefix}_seo_title"),
                'description' => data_get($setting, "{$prefix}_seo_description"),
                'h1' => data_get($setting, "{$prefix}_seo_h1"),
                'og_image' => data_get($setting, "{$prefix}_seo_og_image"),
                'canonical_url' => data_get($setting, "{$prefix}_seo_canonical_url"),
                'robots' => data_get($setting, "{$prefix}_seo_robots"),
            ];
        }

        return $pages;
    }
}
