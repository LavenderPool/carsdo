<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarConfigurationRequest;
use App\Http\Requests\Admin\UpdateCarConfigurationRequest;
use App\Models\Car;
use App\Models\CarConfiguration;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarConfigurationController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->configurations()
            ->with('group:id,name')
            ->latest()
            ->get()
            ->map(fn (CarConfiguration $item) => [
                'id' => $item->id,
                'group' => $item->group?->name,
                'import_index' => $item->import_index,
                'price' => $item->price,
                'engine_type' => $item->engine_type,
                'horsepower' => $item->horsepower,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Комплектации',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'group', 'label' => 'Группа'],
                ['key' => 'import_index', 'label' => 'Import index'],
                ['key' => 'price', 'label' => 'Цена'],
                ['key' => 'engine_type', 'label' => 'Двигатель'],
                ['key' => 'horsepower', 'label' => 'Л.с.'],
            ],
            'createUrl' => route('admin.cars.configurations.create', $car),
            'editBaseUrl' => route('admin.cars.configurations.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.configurations.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить комплектацию',
            'emptyMessage' => 'Комплектации пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить комплектацию #{id}?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новая комплектация',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.configurations.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.configurations.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'car_configuration_group_id' => null,
                'import_index' => null,
                'price' => null,
                'engine_type' => '',
                'engine_capacity' => null,
                'horsepower' => null,
                'transmission' => '',
                'drive_type' => '',
                'fuel_city' => null,
                'fuel_highway' => null,
                'fuel_combined' => null,
                'acceleration' => null,
                'speed' => null,
            ],
            'fields' => [
                ['name' => 'car_configuration_group_id', 'label' => 'Группа', 'type' => 'select', 'required' => true, 'options' => $this->groupOptions($car)],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'price', 'label' => 'Цена', 'type' => 'number'],
                ['name' => 'engine_type', 'label' => 'Тип двигателя', 'type' => 'text'],
                ['name' => 'engine_capacity', 'label' => 'Объем двигателя', 'type' => 'number'],
                ['name' => 'horsepower', 'label' => 'Л.с.', 'type' => 'number'],
                ['name' => 'transmission', 'label' => 'Коробка', 'type' => 'text'],
                ['name' => 'drive_type', 'label' => 'Привод', 'type' => 'text'],
                ['name' => 'fuel_city', 'label' => 'Расход (город)', 'type' => 'number'],
                ['name' => 'fuel_highway', 'label' => 'Расход (трасса)', 'type' => 'number'],
                ['name' => 'fuel_combined', 'label' => 'Расход (смешанный)', 'type' => 'number'],
                ['name' => 'acceleration', 'label' => 'Разгон', 'type' => 'number'],
                ['name' => 'speed', 'label' => 'Макс. скорость', 'type' => 'number'],
            ],
        ]);
    }

    public function store(StoreCarConfigurationRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();
        abort_unless($car->configurationGroups()->whereKey($data['car_configuration_group_id'])->exists(), 422);

        $car->configurations()->create($data);

        return redirect()
            ->route('admin.cars.configurations.index', $car)
            ->with('success', 'Комплектация добавлена.');
    }

    public function edit(Car $car, CarConfiguration $configuration): Response
    {
        abort_unless($configuration->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование комплектации',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.configurations.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.configurations.update', [$car, $configuration]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'car_configuration_group_id' => $configuration->car_configuration_group_id,
                'import_index' => $configuration->import_index,
                'price' => $configuration->price,
                'engine_type' => $configuration->engine_type,
                'engine_capacity' => $configuration->engine_capacity,
                'horsepower' => $configuration->horsepower,
                'transmission' => $configuration->transmission,
                'drive_type' => $configuration->drive_type,
                'fuel_city' => $configuration->fuel_city,
                'fuel_highway' => $configuration->fuel_highway,
                'fuel_combined' => $configuration->fuel_combined,
                'acceleration' => $configuration->acceleration,
                'speed' => $configuration->speed,
            ],
            'fields' => [
                ['name' => 'car_configuration_group_id', 'label' => 'Группа', 'type' => 'select', 'required' => true, 'options' => $this->groupOptions($car)],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'price', 'label' => 'Цена', 'type' => 'number'],
                ['name' => 'engine_type', 'label' => 'Тип двигателя', 'type' => 'text'],
                ['name' => 'engine_capacity', 'label' => 'Объем двигателя', 'type' => 'number'],
                ['name' => 'horsepower', 'label' => 'Л.с.', 'type' => 'number'],
                ['name' => 'transmission', 'label' => 'Коробка', 'type' => 'text'],
                ['name' => 'drive_type', 'label' => 'Привод', 'type' => 'text'],
                ['name' => 'fuel_city', 'label' => 'Расход (город)', 'type' => 'number'],
                ['name' => 'fuel_highway', 'label' => 'Расход (трасса)', 'type' => 'number'],
                ['name' => 'fuel_combined', 'label' => 'Расход (смешанный)', 'type' => 'number'],
                ['name' => 'acceleration', 'label' => 'Разгон', 'type' => 'number'],
                ['name' => 'speed', 'label' => 'Макс. скорость', 'type' => 'number'],
            ],
        ]);
    }

    public function update(
        UpdateCarConfigurationRequest $request,
        Car $car,
        CarConfiguration $configuration
    ): RedirectResponse {
        abort_unless($configuration->car_id === $car->id, 404);
        $data = $request->validated();
        abort_unless($car->configurationGroups()->whereKey($data['car_configuration_group_id'])->exists(), 422);
        $configuration->update($data);

        return redirect()
            ->route('admin.cars.configurations.index', $car)
            ->with('success', 'Комплектация обновлена.');
    }

    public function destroy(Car $car, CarConfiguration $configuration): RedirectResponse
    {
        abort_unless($configuration->car_id === $car->id, 404);
        $configuration->delete();

        return redirect()
            ->route('admin.cars.configurations.index', $car)
            ->with('success', 'Комплектация удалена.');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function groupOptions(Car $car): array
    {
        return $car->configurationGroups()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($group) => ['value' => $group->id, 'label' => $group->name ?? "Group #{$group->id}"])
            ->all();
    }
}
