<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Setting;
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

        View::composer(['layouts.site', 'site.*'], static function ($view): void {
            $setting = Setting::query()->firstOrCreate(
                ['id' => 1],
                ['brand_name' => 'carsDo'],
            );
            $brands = Brand::query()
                ->select(['name', 'slug', 'leave_from_russian'])
                ->orderBy('name')
                ->get();

            $view->with('siteBrandName', $setting->brand_name ?: 'carsDo');
            $view->with(
                'siteFaviconUrl',
                $setting->favicon_path
                    ? asset('storage/'.$setting->favicon_path)
                    : asset('favicon.ico'),
            );
            $view->with('footerBrandsActive', $brands->where('leave_from_russian', false)->values());
            $view->with('footerBrandsLeft', $brands->where('leave_from_russian', true)->values());
        });
    }
}
