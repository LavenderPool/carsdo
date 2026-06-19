<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarConfigurationEquipmentCategoryRequest;
use App\Http\Requests\Admin\UpdateCarConfigurationEquipmentCategoryRequest;
use App\Models\Car;
use App\Models\CarConfigurationEquipmentCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarConfigurationEquipmentCategoryController extends Controller
{
    public function index(Car $car): Response
    {
        $items = CarConfigurationEquipmentCategory::query()
            ->whereHas('configuration', fn ($query) => $query->where('car_id', $car->id))
            ->with(['configuration:id,car_id,car_configuration_group_id', 'configuration.group:id,name'])
            ->latest()
            ->get()
            ->map(fn (CarConfigurationEquipmentCategory $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'group' => $item->configuration?->group?->name,
                'car_configuration_id' => $item->car_configuration_id,
                'import_index' => $item->import_index,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Категории оснащения',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'name', 'label' => 'Название'],
                ['key' => 'group', 'label' => 'Группа'],
                ['key' => 'car_configuration_id', 'label' => 'ID комплектации'],
                ['key' => 'import_index', 'label' => 'Import index'],
            ],
            'createUrl' => route('admin.cars.equipment-categories.create', $car),
            'editBaseUrl' => route('admin.cars.equipment-categories.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.equipment-categories.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить категорию',
            'emptyMessage' => 'Категории оснащения пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить категорию "#{name}"?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новая категория оснащения',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.equipment-categories.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.equipment-categories.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'car_configuration_id' => null,
                'name' => '',
                'import_index' => null,
            ],
            'fields' => [
                ['name' => 'car_configuration_id', 'label' => 'Комплектация', 'type' => 'select', 'options' => $this->configurationOptions($car)],
                ['name' => 'name', 'label' => 'Название', 'type' => 'text', 'required' => true],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
            ],
        ]);
    }

    public function store(StoreCarConfigurationEquipmentCategoryRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();
        abort_unless($car->configurations()->whereKey($data['car_configuration_id'])->exists(), 422);

        CarConfigurationEquipmentCategory::query()->create($data);

        return redirect()
            ->route('admin.cars.equipment-categories.index', $car)
            ->with('success', 'Категория оснащения добавлена.');
    }

    public function edit(Car $car, CarConfigurationEquipmentCategory $equipmentCategory): Response
    {
        abort_unless($equipmentCategory->configuration?->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование категории оснащения',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.equipment-categories.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.equipment-categories.update', [$car, $equipmentCategory]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'car_configuration_id' => $equipmentCategory->car_configuration_id,
                'name' => $equipmentCategory->name,
                'import_index' => $equipmentCategory->import_index,
            ],
            'fields' => [
                ['name' => 'car_configuration_id', 'label' => 'Комплектация', 'type' => 'select', 'options' => $this->configurationOptions($car)],
                ['name' => 'name', 'label' => 'Название', 'type' => 'text', 'required' => true],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
            ],
        ]);
    }

    public function update(
        UpdateCarConfigurationEquipmentCategoryRequest $request,
        Car $car,
        CarConfigurationEquipmentCategory $equipmentCategory
    ): RedirectResponse {
        abort_unless($equipmentCategory->configuration?->car_id === $car->id, 404);

        $data = $request->validated();
        abort_unless($car->configurations()->whereKey($data['car_configuration_id'])->exists(), 422);

        $equipmentCategory->update($data);

        return redirect()
            ->route('admin.cars.equipment-categories.index', $car)
            ->with('success', 'Категория оснащения обновлена.');
    }

    public function destroy(Car $car, CarConfigurationEquipmentCategory $equipmentCategory): RedirectResponse
    {
        abort_unless($equipmentCategory->configuration?->car_id === $car->id, 404);
        CarConfigurationEquipmentCategory::query()->whereKey($equipmentCategory->id)->delete();

        return redirect()
            ->route('admin.cars.equipment-categories.index', $car)
            ->with('success', 'Категория оснащения удалена.');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function configurationOptions(Car $car): array
    {
        return $car->configurations()
            ->with('group:id,name')
            ->orderBy('id')
            ->get(['id', 'car_configuration_group_id'])
            ->map(fn ($configuration) => [
                'value' => $configuration->id,
                'label' => trim(($configuration->group?->name ?? 'Configuration')." #{$configuration->id}"),
            ])
            ->all();
    }
}
