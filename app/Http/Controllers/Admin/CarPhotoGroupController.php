<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarPhotoGroupRequest;
use App\Http\Requests\Admin\UpdateCarPhotoGroupRequest;
use App\Models\Car;
use App\Models\CarPhotoGroup;
use App\Support\Media\CarMediaStorage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarPhotoGroupController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->photoGroups()
            ->latest()
            ->get()
            ->map(fn (CarPhotoGroup $item) => [
                'id' => $item->id,
                'name' => $item->name,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Группы фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'name', 'label' => 'Название'],
            ],
            'createUrl' => route('admin.cars.photo-groups.create', $car),
            'editBaseUrl' => route('admin.cars.photo-groups.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.photo-groups.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить группу фото',
            'emptyMessage' => 'Группы фото пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить группу "#{name}"?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новая группа фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.photo-groups.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.photo-groups.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'name' => '',
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Название', 'type' => 'text', 'required' => true],
            ],
        ]);
    }

    public function store(StoreCarPhotoGroupRequest $request, Car $car): RedirectResponse
    {
        $car->photoGroups()->create($request->validated());

        return redirect()
            ->route('admin.cars.photo-groups.index', $car)
            ->with('success', 'Группа фото добавлена.');
    }

    public function edit(Car $car, CarPhotoGroup $photoGroup): Response
    {
        abort_unless($photoGroup->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование группы фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.photo-groups.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.photo-groups.update', [$car, $photoGroup]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'name' => $photoGroup->name,
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Название', 'type' => 'text', 'required' => true],
            ],
        ]);
    }

    public function update(UpdateCarPhotoGroupRequest $request, Car $car, CarPhotoGroup $photoGroup): RedirectResponse
    {
        abort_unless($photoGroup->car_id === $car->id, 404);
        $photoGroup->update($request->validated());

        return redirect()
            ->route('admin.cars.photo-groups.index', $car)
            ->with('success', 'Группа фото обновлена.');
    }

    public function destroy(Car $car, CarPhotoGroup $photoGroup): RedirectResponse
    {
        abort_unless($photoGroup->car_id === $car->id, 404);
        $photoGroup->load('photos');
        CarMediaStorage::deletePhotoFiles($photoGroup->photos);
        $photoGroup->photos()->delete();
        CarPhotoGroup::query()->whereKey($photoGroup->id)->delete();

        return redirect()
            ->route('admin.cars.photo-groups.index', $car)
            ->with('success', 'Группа фото удалена.');
    }
}
