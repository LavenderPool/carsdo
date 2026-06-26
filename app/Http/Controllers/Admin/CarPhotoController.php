<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarPhotoRequest;
use App\Http\Requests\Admin\UpdateCarPhotoRequest;
use App\Models\Car;
use App\Models\CarPhoto;
use App\Support\Media\CarMediaStorage;
use App\Support\Media\MediaVariantService;
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
                'photo_url' => $item->url(),
                'preview_url' => $item->url(),
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Фото',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'preview_url', 'label' => 'Превью', 'type' => 'image'],
                ['key' => 'group', 'label' => 'Группа'],
                ['key' => 'photo_url', 'label' => 'Ссылка', 'type' => 'link'],
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
                'photo' => null,
            ],
            'fields' => [
                ['name' => 'car_photo_group_id', 'label' => 'Группа', 'type' => 'select', 'required' => true, 'options' => $this->groupOptions($car)],
                ['name' => 'photo', 'label' => 'Файл', 'type' => 'file', 'required' => true, 'accept' => 'image/*'],
            ],
        ]);
    }

    public function store(StoreCarPhotoRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();
        abort_unless($car->photoGroups()->whereKey($data['car_photo_group_id'])->exists(), 422);

        $photo = $car->photos()->create([
            'car_photo_group_id' => $data['car_photo_group_id'],
            'photo_path' => $this->storeUploadedPhoto($request, $car),
        ]);
        app(MediaVariantService::class)->ensureWebpVariant($photo->photo_path, CarPhoto::class, $photo->id);

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
                'photo' => null,
                'photo_url' => $photo->url(),
                'photo_path' => $photo->photo_path,
            ],
            'fields' => [
                ['name' => 'car_photo_group_id', 'label' => 'Группа', 'type' => 'select', 'required' => true, 'options' => $this->groupOptions($car)],
                ['name' => 'photo', 'label' => 'Новый файл', 'type' => 'file', 'required' => false, 'accept' => 'image/*'],
            ],
        ]);
    }

    public function update(UpdateCarPhotoRequest $request, Car $car, CarPhoto $photo): RedirectResponse
    {
        abort_unless($photo->car_id === $car->id, 404);
        $data = $request->validated();
        abort_unless($car->photoGroups()->whereKey($data['car_photo_group_id'])->exists(), 422);

        $attributes = [
            'car_photo_group_id' => $data['car_photo_group_id'],
        ];

        if ($request->hasFile('photo')) {
            CarMediaStorage::deletePhotoFile($photo);
            $attributes['photo_path'] = $this->storeUploadedPhoto($request, $car);
        }

        $photo->update($attributes);
        app(MediaVariantService::class)->ensureWebpVariant($photo->photo_path, CarPhoto::class, $photo->id);

        return redirect()
            ->route('admin.cars.photos.index', $car)
            ->with('success', 'Фото обновлено.');
    }

    public function destroy(Car $car, CarPhoto $photo): RedirectResponse
    {
        abort_unless($photo->car_id === $car->id, 404);
        CarMediaStorage::deletePhotoFile($photo);
        CarPhoto::query()->whereKey($photo->id)->delete();

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

    private function storeUploadedPhoto(StoreCarPhotoRequest|UpdateCarPhotoRequest $request, Car $car): string
    {
        return $request->file('photo')->store(
            'images/'.$car->brand->slug.'/'.$car->slug,
            'public',
        );
    }
}
