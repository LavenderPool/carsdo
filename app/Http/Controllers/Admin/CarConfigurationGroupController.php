<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarConfigurationGroupRequest;
use App\Http\Requests\Admin\UpdateCarConfigurationGroupRequest;
use App\Models\Car;
use App\Models\CarConfigurationGroup;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarConfigurationGroupController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->configurationGroups()
            ->orderBy('order')
            ->latest()
            ->get()
            ->map(fn (CarConfigurationGroup $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'order' => $item->order,
                'import_index' => $item->import_index,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Группы комплектаций',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'name', 'label' => 'Название'],
                ['key' => 'order', 'label' => 'Порядок'],
                ['key' => 'import_index', 'label' => 'Import index'],
            ],
            'createUrl' => route('admin.cars.configuration-groups.create', $car),
            'editBaseUrl' => route('admin.cars.configuration-groups.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.configuration-groups.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить группу',
            'emptyMessage' => 'Группы комплектаций пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить группу "#{name}"?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новая группа комплектаций',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.configuration-groups.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.configuration-groups.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'name' => '',
                'order' => null,
                'import_index' => null,
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Название', 'type' => 'text'],
                ['name' => 'order', 'label' => 'Порядок', 'type' => 'number'],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
            ],
        ]);
    }

    public function store(StoreCarConfigurationGroupRequest $request, Car $car): RedirectResponse
    {
        $car->configurationGroups()->create($request->validated());

        return redirect()
            ->route('admin.cars.configuration-groups.index', $car)
            ->with('success', 'Группа комплектаций добавлена.');
    }

    public function edit(Car $car, CarConfigurationGroup $configurationGroup): Response
    {
        abort_unless($configurationGroup->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование группы комплектаций',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.configuration-groups.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.configuration-groups.update', [$car, $configurationGroup]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'name' => $configurationGroup->name,
                'order' => $configurationGroup->order,
                'import_index' => $configurationGroup->import_index,
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Название', 'type' => 'text'],
                ['name' => 'order', 'label' => 'Порядок', 'type' => 'number'],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
            ],
        ]);
    }

    public function update(
        UpdateCarConfigurationGroupRequest $request,
        Car $car,
        CarConfigurationGroup $configurationGroup
    ): RedirectResponse {
        abort_unless($configurationGroup->car_id === $car->id, 404);
        $configurationGroup->update($request->validated());

        return redirect()
            ->route('admin.cars.configuration-groups.index', $car)
            ->with('success', 'Группа комплектаций обновлена.');
    }

    public function destroy(Car $car, CarConfigurationGroup $configurationGroup): RedirectResponse
    {
        abort_unless($configurationGroup->car_id === $car->id, 404);
        $configurationGroup->delete();

        return redirect()
            ->route('admin.cars.configuration-groups.index', $car)
            ->with('success', 'Группа комплектаций удалена.');
    }
}
