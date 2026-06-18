<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\City;
use App\Support\Cache\SiteCache;
use App\Support\RecentViews;
use Illuminate\Contracts\View\View;

class CarController extends Controller
{
    public function show(Brand $brand, Car $car, RecentViews $recentViews): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        if ($recentViews->remember("car:{$car->id}")) {
            $car->incrementQuietly('views_count');
        }

        $car = SiteCache::remember("car:{$car->id}:show", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'crashTest:car_id,year,rating,video_path',
                'testDrives:id,car_id,import_index,author,video_path',
                'reviews:id,car_id,import_index,type,value',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
                'carDealers' => fn ($query) => $query
                    ->select('id', 'car_id', 'city_id', 'dealer_id', 'address', 'phone', 'website', 'is_official')
                    ->with([
                        'city:id,name,slug',
                        'dealer:id,name',
                    ])
                    ->orderBy('city_id')
                    ->orderBy('dealer_id'),
            ])
            ->firstOrFail());

        return view('site.car.index', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
        ]);
    }

    public function testDrive(Brand $brand, Car $car): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        $car = SiteCache::remember("car:{$car->id}:test-drive", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'testDrives:id,car_id,import_index,author,video_path',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'reviews:id,car_id,import_index,type,value',
                'crashTest:id,car_id,year,rating,video_path',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
            ])
            ->firstOrFail());

        abort_unless($car->testDrives->isNotEmpty(), 404);

        return view('site.car.test-drive', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
        ]);
    }

    public function crashTest(Brand $brand, Car $car): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        $car = SiteCache::remember("car:{$car->id}:crash-test", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'crashTest:id,car_id,year,rating,video_path',
                'testDrives:id,car_id,import_index,author,video_path',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'reviews:id,car_id,import_index,type,value',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
            ])
            ->firstOrFail());

        abort_unless($car->crashTest !== null, 404);

        return view('site.car.crash-test', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
        ]);
    }

    public function reviews(Brand $brand, Car $car): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        $car = SiteCache::remember("car:{$car->id}:reviews", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'reviews:id,car_id,import_index,type,value',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
            ])
            ->firstOrFail());

        abort_unless($car->reviews->isNotEmpty(), 404);

        return view('site.car.reviews', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
        ]);
    }

    public function photo(Brand $brand, Car $car): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        $car = SiteCache::remember("car:{$car->id}:photo", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'crashTest:id,car_id,year,rating,video_path',
                'testDrives:id,car_id,import_index,author,video_path',
                'reviews:id,car_id,import_index,type,value',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
            ])
            ->firstOrFail());

        $hasAnyPhotos = $car->photos->contains(fn ($photo) => filled($photo->photo_path))
            || $car->photoGroups->flatMap->photos->contains(fn ($photo) => filled($photo->photo_path));

        abort_unless($hasAnyPhotos, 404);

        return view('site.car.photo', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
        ]);
    }

    public function equipment(Brand $brand, Car $car, int $order): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        $car = SiteCache::remember("car:{$car->id}:equipment", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'crashTest:id,car_id,year,rating,video_path',
                'testDrives:id,car_id,import_index,author,video_path',
                'reviews:id,car_id,import_index,type,value',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurationGroups.equipmentCategories:id,car_configuration_group_id,name,import_index',
                'configurationGroups.equipmentCategories.items:id,car_configuration_equipment_category_id,import_index,value,is_extension,price',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
                'carDealers' => fn ($query) => $query
                    ->select('id', 'car_id', 'city_id', 'dealer_id', 'address', 'phone', 'website', 'is_official')
                    ->with([
                        'city:id,name,slug',
                        'dealer:id,name',
                    ])
                    ->orderBy('city_id')
                    ->orderBy('dealer_id'),
            ])
            ->firstOrFail());

        $configurationGroups = $car->configurationGroups
            ->sortBy([
                ['order', 'asc'],
                ['import_index', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        $selectedGroup = $configurationGroups->get($order - 1);
        abort_if($selectedGroup === null, 404);

        return view('site.car.equipment', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
            'selectedGroup' => $selectedGroup,
            'selectedOrder' => $order,
        ]);
    }

    public function dealer(Brand $brand, Car $car, City $city): View
    {
        abort_if($car->brand_id !== $brand->id, 404);

        $car = SiteCache::remember("car:{$car->id}:dealer", static fn () => Car::query()
            ->whereKey($car->id)
            ->with([
                'brand:id,name,slug',
                'crashTest:id,car_id,year,rating,video_path',
                'testDrives:id,car_id,import_index,author,video_path',
                'reviews:id,car_id,import_index,type,value',
                'configurationGroups:id,car_id,name,order,import_index',
                'configurations:id,car_id,car_configuration_group_id,import_index,price,engine_type,engine_capacity,horsepower,transmission,drive_type,fuel_city,fuel_highway,fuel_combined,acceleration,speed',
                'photoGroups:id,car_id,name',
                'photoGroups.photos:id,car_id,car_photo_group_id,photo_path',
                'photos:id,car_id,car_photo_group_id,photo_path',
                'carDealers' => fn ($query) => $query
                    ->select('id', 'car_id', 'city_id', 'dealer_id', 'address', 'phone', 'website', 'is_official')
                    ->with([
                        'city:id,name,slug',
                        'dealer:id,name',
                    ])
                    ->orderBy('city_id')
                    ->orderByDesc('is_official')
                    ->orderBy('dealer_id'),
            ])
            ->firstOrFail());

        $cityDealers = $car->carDealers
            ->where('city_id', $city->id)
            ->values();

        abort_unless($cityDealers->isNotEmpty(), 404);

        return view('site.car.dealer', [
            'brand' => $this->brandWithSidebar($brand),
            'car' => $car,
            'city' => $city,
            'cityDealers' => $cityDealers,
        ]);
    }

    private function brandWithSidebar(Brand $brand): Brand
    {
        $cars = SiteCache::remember("brand:{$brand->id}:sidebar", static fn () => $brand->cars()
            ->select('id', 'brand_id', 'name', 'slug', 'start_price', 'is_soon', 'is_another_models')
            ->orderBy('name')
            ->get());

        $brand->setRelation('cars', $cars);

        return $brand;
    }
}
