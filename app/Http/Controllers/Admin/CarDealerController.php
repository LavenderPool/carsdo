<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarDealerRequest;
use App\Http\Requests\Admin\UpdateCarDealerRequest;
use App\Models\Car;
use App\Models\CarDealer;
use App\Models\City;
use App\Models\Dealer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CarDealerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $carId = $request->filled('car_id') ? $request->integer('car_id') : null;
        $dealerId = $request->filled('dealer_id') ? $request->integer('dealer_id') : null;
        $cityId = $request->filled('city_id') ? $request->integer('city_id') : null;

        $carDealers = CarDealer::query()
            ->with(['car.brand:id,name', 'dealer:id,name', 'city:id,name'])
            ->when($carId, fn ($query) => $query->where('car_id', $carId))
            ->when($dealerId, fn ($query) => $query->where('dealer_id', $dealerId))
            ->when($cityId, fn ($query) => $query->where('city_id', $cityId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('address', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('website', 'like', "%{$search}%")
                        ->orWhereHas('dealer', fn ($dealerQuery) => $dealerQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('city', fn ($cityQuery) => $cityQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('car', fn ($carQuery) => $carQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (CarDealer $carDealer) => [
                'id' => $carDealer->id,
                'car' => $this->carLabel($carDealer->car),
                'dealer' => $carDealer->dealer?->name ?? '-',
                'city' => $carDealer->city?->name ?? '-',
                'address' => $carDealer->address,
                'phone' => $carDealer->phone,
                'website' => $carDealer->website,
                'is_official' => $carDealer->is_official,
                'created_at' => $carDealer->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/CarDealers/Index', [
            'carDealers' => $carDealers,
            'filters' => [
                'search' => $search,
                'car_id' => $carId,
                'dealer_id' => $dealerId,
                'city_id' => $cityId,
            ],
            'options' => $this->formOptions(),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/CarDealers/Create', [
            'options' => $this->formOptions(),
        ]);
    }

    public function store(StoreCarDealerRequest $request): RedirectResponse
    {
        CarDealer::query()->create($request->validated());

        return redirect()
            ->route('admin.car-dealers.index')
            ->with('success', 'Связка дилера создана.');
    }

    public function edit(CarDealer $carDealer): Response
    {
        $carDealer->loadMissing(['car.brand:id,name', 'dealer:id,name', 'city:id,name']);

        return Inertia::render('Admin/CarDealers/Edit', [
            'carDealer' => [
                'id' => $carDealer->id,
                'car_id' => $carDealer->car_id,
                'dealer_id' => $carDealer->dealer_id,
                'city_id' => $carDealer->city_id,
                'address' => $carDealer->address,
                'phone' => $carDealer->phone,
                'website' => $carDealer->website,
                'is_official' => $carDealer->is_official,
            ],
            'options' => $this->formOptions(),
        ]);
    }

    public function update(UpdateCarDealerRequest $request, CarDealer $carDealer): RedirectResponse
    {
        $carDealer->update($request->validated());

        return redirect()
            ->route('admin.car-dealers.index')
            ->with('success', 'Связка дилера обновлена.');
    }

    public function destroy(CarDealer $carDealer): RedirectResponse
    {
        CarDealer::query()
            ->whereKey($carDealer->id)
            ->delete();

        return redirect()
            ->route('admin.car-dealers.index')
            ->with('success', 'Связка дилера удалена.');
    }

    /**
     * @return array{
     *     cars: array<int, array{id: int, label: string}>,
     *     dealers: array<int, array{id: int, name: string}>,
     *     cities: array<int, array{id: int, name: string}>
     * }
     */
    private function formOptions(): array
    {
        return [
            'cars' => Car::query()
                ->with('brand:id,name')
                ->orderBy('name')
                ->get()
                ->map(fn (Car $car) => [
                    'id' => $car->id,
                    'label' => $this->carLabel($car),
                ])
                ->all(),
            'dealers' => Dealer::query()
                ->orderBy('name', 'asc')
                ->get(['id', 'name'])
                ->map(fn (Dealer $dealer) => [
                    'id' => $dealer->id,
                    'name' => $dealer->name,
                ])
                ->all(),
            'cities' => City::query()
                ->orderBy('name', 'asc')
                ->get(['id', 'name'])
                ->map(fn (City $city) => [
                    'id' => $city->id,
                    'name' => $city->name,
                ])
                ->all(),
        ];
    }

    private function carLabel(?Car $car): string
    {
        if (! $car) {
            return '-';
        }

        return $car->name;
    }
}
