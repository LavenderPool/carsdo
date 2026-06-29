<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEngineRequest;
use App\Http\Requests\Admin\UpdateEngineRequest;
use App\Models\Brand;
use App\Models\Engine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EngineController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));

        $engines = Engine::query()
            ->with('brand:id,name,slug')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('brand', function ($brandQuery) use ($search): void {
                            $brandQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('slug', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Engine $engine) => [
                'id' => $engine->id,
                'name' => $engine->name,
                'slug' => $engine->slug,
                'brand' => [
                    'id' => $engine->brand?->id,
                    'name' => $engine->brand?->name,
                    'slug' => $engine->brand?->slug,
                ],
                'engine_type' => $engine->engine_type,
                'created_at' => $engine->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Engines/Index', [
            'engines' => $engines,
            'filters' => [
                'search' => $search,
            ],
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Engines/Create', [
            'brands' => $this->brandOptions(),
        ]);
    }

    public function store(StoreEngineRequest $request): RedirectResponse
    {
        Engine::query()->create($request->validated());

        return redirect()
            ->route('admin.engines.index')
            ->with('success', 'Двигатель создан.');
    }

    public function edit(Engine $engine): Response
    {
        return Inertia::render('Admin/Engines/Edit', [
            'engine' => $this->enginePayload($engine),
            'brands' => $this->brandOptions(),
        ]);
    }

    public function update(UpdateEngineRequest $request, Engine $engine): RedirectResponse
    {
        $engine->update($request->validated());

        return redirect()
            ->route('admin.engines.index')
            ->with('success', 'Двигатель обновлен.');
    }

    public function destroy(Engine $engine): RedirectResponse
    {
        $engine->delete();

        return redirect()
            ->route('admin.engines.index')
            ->with('success', 'Двигатель удален.');
    }

    /**
     * @return array<int, array{id: int, name: string, slug: string}>
     */
    private function brandOptions(): array
    {
        return Brand::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Brand $brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function enginePayload(Engine $engine): array
    {
        return [
            'id' => $engine->id,
            'brand_id' => $engine->brand_id,
            'name' => $engine->name,
            'slug' => $engine->slug,
            'engine_url' => $engine->engine_url,
            'engine_type' => $engine->engine_type,
            'displacement_cc' => $engine->displacement_cc,
            'max_horsepower' => $engine->max_horsepower,
            'max_power_output_at_rpm' => $engine->max_power_output_at_rpm,
            'max_torque_at_rpm' => $engine->max_torque_at_rpm,
            'valves_per_cylinder' => $engine->valves_per_cylinder,
            'compression_ratio' => $engine->compression_ratio,
            'cylinder_bore_mm' => $engine->cylinder_bore_mm,
            'piston_stroke_mm' => $engine->piston_stroke_mm,
            'valvetrain' => $engine->valvetrain,
            'recommended_fuel_type' => $engine->recommended_fuel_type,
            'fuel_consumption_l_per_100_km' => $engine->fuel_consumption_l_per_100_km,
            'co2_emissions_g_per_km' => $engine->co2_emissions_g_per_km,
            'has_start_stop_system' => $engine->has_start_stop_system,
            'engine_notes' => $engine->engine_notes,
            'page_text' => $engine->page_text,
        ];
    }
}
