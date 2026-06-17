<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'admin';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'settings' => fn () => $this->settingsPayload(),
        ];
    }

    /**
     * @return array{brand_name: string, favicon_url: string}
     */
    private function settingsPayload(): array
    {
        $setting = Setting::query()->firstOrCreate(
            ['id' => 1],
            ['brand_name' => 'carsDo'],
        );

        return [
            'brand_name' => $setting->brand_name ?: 'carsDo',
            'favicon_url' => $setting->favicon_path
                ? asset('storage/'.$setting->favicon_path)
                : asset('favicon.ico'),
        ];
    }
}
