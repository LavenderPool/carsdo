<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarPhotoRequest;
use App\Http\Requests\Admin\UpdateCarPhotoRequest;
use App\Models\Car;
use App\Models\CarPhoto;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarPhotoController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->photos()
            ->with('group:id,name')
            ->latest()
            ->get()
            ->map(fn (CarPhoto $item) => [
                'id' => $item->id,
                'group' => $item->group?->name,
                'photo_path' => $item->photo_path,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'group', 'label' => 'Группа'],
                ['key' => 'photo_path', 'label' => 'Путь'],
            ],
            'createUrl' => route('admin.cars.photos.create', $car),
            'editBaseUrl' => route('admin.cars.photos.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.photos.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить фото',
            'emptyMessage' => 'Фото пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить фото #{id}?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новое фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.photos.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.photos.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'car_photo_group_id' => null,
                'photo_path' => '',
            ],
            'fields' => [
                ['name' => 'car_photo_group_id', 'label' => 'Группа', 'type' => 'select', 'required' => true, 'options' => $this->groupOptions($car)],
                ['name' => 'photo_path', 'label' => 'Путь к фото', 'type' => 'text', 'required' => true],
            ],
        ]);
    }

    public function store(StoreCarPhotoRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();
        abort_unless($car->photoGroups()->whereKey($data['car_photo_group_id'])->exists(), 422);

        $car->photos()->create($data);

        return redirect()
            ->route('admin.cars.photos.index', $car)
            ->with('success', 'Фото добавлено.');
    }

    public function edit(Car $car, CarPhoto $photo): Response
    {
        abort_unless($photo->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.photos.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.photos.update', [$car, $photo]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'car_photo_group_id' => $photo->car_photo_group_id,
                'photo_path' => $photo->photo_path,
            ],
            'fields' => [
                ['name' => 'car_photo_group_id', 'label' => 'Группа', 'type' => 'select', 'required' => true, 'options' => $this->groupOptions($car)],
                ['name' => 'photo_path', 'label' => 'Путь к фото', 'type' => 'text', 'required' => true],
            ],
        ]);
    }

    public function update(UpdateCarPhotoRequest $request, Car $car, CarPhoto $photo): RedirectResponse
    {
        abort_unless($photo->car_id === $car->id, 404);
        $data = $request->validated();
        abort_unless($car->photoGroups()->whereKey($data['car_photo_group_id'])->exists(), 422);
        $photo->update($data);

        return redirect()
            ->route('admin.cars.photos.index', $car)
            ->with('success', 'Фото обновлено.');
    }

    public function destroy(Car $car, CarPhoto $photo): RedirectResponse
    {
        abort_unless($photo->car_id === $car->id, 404);
        $photo->delete();

        return redirect()
            ->route('admin.cars.photos.index', $car)
            ->with('success', 'Фото удалено.');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function groupOptions(Car $car): array
    {
        return $car->photoGroups()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($group) => ['value' => $group->id, 'label' => $group->name])
            ->all();
    }
}
