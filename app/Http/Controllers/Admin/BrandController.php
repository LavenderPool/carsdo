<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));

        $brands = Brand::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Brand $brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'leave_from_russian' => $brand->leave_from_russian,
                'views_count' => $brand->views_count,
                'created_at' => $brand->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => [
                'search' => $search,
            ],
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Brands/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        Brand::create($request->validated());

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Бренд создан.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand): Response
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => array_merge([
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'leave_from_russian' => $brand->leave_from_russian,
            ], $brand->only(AdminSeoFields::brandFields())),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $brand->update($request->validated());

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Бренд обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        Brand::query()
            ->whereKey($brand->id)
            ->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Бренд удален.');
    }
}
