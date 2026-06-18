<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarConfigurationEquipmentRequest;
use App\Http\Requests\Admin\UpdateCarConfigurationEquipmentRequest;
use App\Models\Car;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarConfigurationEquipmentController extends Controller
{
    public function index(Car $car): Response
    {
        $items = CarConfigurationEquipment::query()
            ->whereHas('category.group', fn ($query) => $query->where('car_id', $car->id))
            ->with(['category:id,name'])
            ->latest()
            ->get()
            ->map(fn (CarConfigurationEquipment $item) => [
                'id' => $item->id,
                'category' => $item->category?->name,
                'import_index' => $item->import_index,
                'value' => $item->value,
                'is_extension' => $item->is_extension,
                'price' => $item->price,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Опции оснащения',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'category', 'label' => 'Категория'],
                ['key' => 'import_index', 'label' => 'Import index'],
                ['key' => 'value', 'label' => 'Значение'],
                ['key' => 'is_extension', 'label' => 'Доп. опция'],
                ['key' => 'price', 'label' => 'Цена'],
            ],
            'createUrl' => route('admin.cars.equipment.create', $car),
            'editBaseUrl' => route('admin.cars.equipment.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.equipment.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить опцию',
            'emptyMessage' => 'Опции оснащения пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить опцию #{id}?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новая опция оснащения',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.equipment.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.equipment.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'car_configuration_equipment_category_id' => null,
                'import_index' => null,
                'value' => '',
                'is_extension' => false,
                'price' => null,
            ],
            'fields' => [
                ['name' => 'car_configuration_equipment_category_id', 'label' => 'Категория', 'type' => 'select', 'required' => true, 'options' => $this->categoryOptions($car)],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'value', 'label' => 'Значение', 'type' => 'textarea'],
                ['name' => 'is_extension', 'label' => 'Дополнительная опция', 'type' => 'checkbox'],
                ['name' => 'price', 'label' => 'Цена', 'type' => 'number'],
            ],
        ]);
    }

    public function store(StoreCarConfigurationEquipmentRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();
        $category = $this->resolveCategoryForCar($car, $data['car_configuration_equipment_category_id']);
        $data['car_configuration_id'] = $category->car_configuration_id;

        CarConfigurationEquipment::query()->create($data);

        return redirect()
            ->route('admin.cars.equipment.index', $car)
            ->with('success', 'Опция оснащения добавлена.');
    }

    public function edit(Car $car, CarConfigurationEquipment $equipment): Response
    {
        abort_unless($this->equipmentBelongsToCar($car, $equipment), 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование опции оснащения',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.equipment.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.equipment.update', [$car, $equipment]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'car_configuration_equipment_category_id' => $equipment->car_configuration_equipment_category_id,
                'import_index' => $equipment->import_index,
                'value' => $equipment->value,
                'is_extension' => $equipment->is_extension,
                'price' => $equipment->price,
            ],
            'fields' => [
                ['name' => 'car_configuration_equipment_category_id', 'label' => 'Категория', 'type' => 'select', 'required' => true, 'options' => $this->categoryOptions($car)],
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'value', 'label' => 'Значение', 'type' => 'textarea'],
                ['name' => 'is_extension', 'label' => 'Дополнительная опция', 'type' => 'checkbox'],
                ['name' => 'price', 'label' => 'Цена', 'type' => 'number'],
            ],
        ]);
    }

    public function update(
        UpdateCarConfigurationEquipmentRequest $request,
        Car $car,
        CarConfigurationEquipment $equipment
    ): RedirectResponse {
        abort_unless($this->equipmentBelongsToCar($car, $equipment), 404);

        $data = $request->validated();
        $category = $this->resolveCategoryForCar($car, $data['car_configuration_equipment_category_id']);
        $data['car_configuration_id'] = $category->car_configuration_id;

        $equipment->update($data);

        return redirect()
            ->route('admin.cars.equipment.index', $car)
            ->with('success', 'Опция оснащения обновлена.');
    }

    public function destroy(Car $car, CarConfigurationEquipment $equipment): RedirectResponse
    {
        abort_unless($this->equipmentBelongsToCar($car, $equipment), 404);
        $equipment->delete();

        return redirect()
            ->route('admin.cars.equipment.index', $car)
            ->with('success', 'Опция оснащения удалена.');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function categoryOptions(Car $car): array
    {
        return $car->configurationGroups()
            ->with('equipmentCategories:id,name,car_configuration_group_id')
            ->get()
            ->flatMap(fn ($group) => $group->equipmentCategories->map(
                fn ($category) => ['value' => $category->id, 'label' => $category->name ?? "Category #{$category->id}"]
            ))
            ->values()
            ->all();
    }

    private function categoryBelongsToCar(Car $car, int $categoryId): bool
    {
        return $car->configurationGroups()
            ->whereHas('equipmentCategories', fn ($query) => $query->whereKey($categoryId))
            ->exists();
    }

    private function resolveCategoryForCar(Car $car, int $categoryId): CarConfigurationEquipmentCategory
    {
        abort_unless($this->categoryBelongsToCar($car, $categoryId), 422);

        return CarConfigurationEquipmentCategory::query()
            ->whereKey($categoryId)
            ->firstOrFail();
    }

    private function equipmentBelongsToCar(Car $car, CarConfigurationEquipment $equipment): bool
    {
        return CarConfigurationEquipment::query()
            ->whereKey($equipment->id)
            ->whereHas('category.group', fn ($query) => $query->where('car_id', $car->id))
            ->exists();
    }
}
