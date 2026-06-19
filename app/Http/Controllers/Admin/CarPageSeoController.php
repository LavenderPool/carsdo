<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCarPageSeoRequest;
use App\Models\CarPageSeo;
use App\Support\Seo\AdminSeoFields;
use App\Support\Seo\CarPageSeoRegistry;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarPageSeoController extends Controller
{
    public function index(): Response
    {
        $this->syncPages();

        $pages = CarPageSeo::query()
            ->get()
            ->keyBy('page_key');

        return Inertia::render('Admin/CarPageSeos/Index', [
            'pages' => collect(CarPageSeoRegistry::definitions())
                ->map(function (array $definition, string $pageKey) use ($pages): array {
                    /** @var CarPageSeo|null $page */
                    $page = $pages->get($pageKey);

                    return [
                        'page_key' => $pageKey,
                        'name' => $page?->name ?? $definition['name'],
                        'car_seo_prefix' => $definition['car_seo_prefix'],
                        'has_overrides' => collect(AdminSeoFields::BASE_KEYS)
                            ->contains(fn (string $field): bool => filled(data_get($page, $field))),
                    ];
                })
                ->values(),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function edit(string $pageKey): Response
    {
        $page = $this->resolvePage($pageKey);

        return Inertia::render('Admin/CarPageSeos/Edit', [
            'page' => array_merge([
                'page_key' => $page->page_key,
                'name' => $page->name,
                'car_seo_prefix' => CarPageSeoRegistry::carSeoPrefix($pageKey),
                'placeholders_hint' => CarPageSeoRegistry::placeholdersHint($pageKey),
            ], $page->only(AdminSeoFields::BASE_KEYS)),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function update(UpdateCarPageSeoRequest $request, string $pageKey): RedirectResponse
    {
        $page = $this->resolvePage($pageKey);
        $page->update(array_merge(
            ['name' => CarPageSeoRegistry::name($pageKey)],
            $request->validated(),
        ));

        return redirect()
            ->route('admin.car-page-seos.edit', $pageKey)
            ->with('success', 'SEO страницы обновлено.');
    }

    private function resolvePage(string $pageKey): CarPageSeo
    {
        abort_unless(CarPageSeoRegistry::has($pageKey), 404);

        return CarPageSeo::query()->firstOrCreate(
            ['page_key' => $pageKey],
            ['name' => CarPageSeoRegistry::name($pageKey)],
        );
    }

    private function syncPages(): void
    {
        foreach (CarPageSeoRegistry::definitions() as $pageKey => $definition) {
            CarPageSeo::query()->firstOrCreate(
                ['page_key' => $pageKey],
                ['name' => $definition['name']],
            );
        }
    }
}
