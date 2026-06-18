<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDealerRequest;
use App\Http\Requests\Admin\UpdateDealerRequest;
use App\Models\Dealer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DealerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));

        $dealers = Dealer::query()
            ->withCount('carDealers')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'name' => $dealer->name,
                'car_dealers_count' => $dealer->car_dealers_count,
                'created_at' => $dealer->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Dealers/Index', [
            'dealers' => $dealers,
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
        return Inertia::render('Admin/Dealers/Create');
    }

    public function store(StoreDealerRequest $request): RedirectResponse
    {
        Dealer::query()->create($request->validated());

        return redirect()
            ->route('admin.dealers.index')
            ->with('success', 'Дилер создан.');
    }

    public function edit(Dealer $dealer): Response
    {
        return Inertia::render('Admin/Dealers/Edit', [
            'dealer' => [
                'id' => $dealer->id,
                'name' => $dealer->name,
            ],
        ]);
    }

    public function update(UpdateDealerRequest $request, Dealer $dealer): RedirectResponse
    {
        $dealer->update($request->validated());

        return redirect()
            ->route('admin.dealers.index')
            ->with('success', 'Дилер обновлен.');
    }

    public function destroy(Dealer $dealer): RedirectResponse
    {
        Dealer::query()
            ->whereKey($dealer->id)
            ->delete();

        return redirect()
            ->route('admin.dealers.index')
            ->with('success', 'Дилер удален.');
    }
}
