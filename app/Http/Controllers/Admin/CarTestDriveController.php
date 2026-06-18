<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarTestDriveRequest;
use App\Http\Requests\Admin\UpdateCarTestDriveRequest;
use App\Models\Car;
use App\Models\CarTestDrive;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarTestDriveController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->testDrives()
            ->latest()
            ->get()
            ->map(fn (CarTestDrive $item) => [
                'id' => $item->id,
                'import_index' => $item->import_index,
                'author' => $item->author,
                'video_path' => $item->video_path,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Тест-драйвы',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'import_index', 'label' => 'Import index'],
                ['key' => 'author', 'label' => 'Автор'],
                ['key' => 'video_path', 'label' => 'Видео'],
            ],
            'createUrl' => route('admin.cars.test-drives.create', $car),
            'editBaseUrl' => route('admin.cars.test-drives.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.test-drives.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить тест-драйв',
            'emptyMessage' => 'Тест-драйвы пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить тест-драйв "#{author}"?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новый тест-драйв',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.test-drives.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.test-drives.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'import_index' => null,
                'author' => '',
                'video_path' => '',
            ],
            'fields' => [
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'author', 'label' => 'Автор', 'type' => 'text', 'required' => true],
                ['name' => 'video_path', 'label' => 'Ссылка на видео', 'type' => 'text', 'required' => true],
            ],
        ]);
    }

    public function store(StoreCarTestDriveRequest $request, Car $car): RedirectResponse
    {
        $car->testDrives()->create($request->validated());

        return redirect()
            ->route('admin.cars.test-drives.index', $car)
            ->with('success', 'Тест-драйв добавлен.');
    }

    public function edit(Car $car, CarTestDrive $testDrive): Response
    {
        abort_unless($testDrive->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование тест-драйва',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.test-drives.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.test-drives.update', [$car, $testDrive]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'import_index' => $testDrive->import_index,
                'author' => $testDrive->author,
                'video_path' => $testDrive->video_path,
            ],
            'fields' => [
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'author', 'label' => 'Автор', 'type' => 'text', 'required' => true],
                ['name' => 'video_path', 'label' => 'Ссылка на видео', 'type' => 'text', 'required' => true],
            ],
        ]);
    }

    public function update(UpdateCarTestDriveRequest $request, Car $car, CarTestDrive $testDrive): RedirectResponse
    {
        abort_unless($testDrive->car_id === $car->id, 404);
        $testDrive->update($request->validated());

        return redirect()
            ->route('admin.cars.test-drives.index', $car)
            ->with('success', 'Тест-драйв обновлен.');
    }

    public function destroy(Car $car, CarTestDrive $testDrive): RedirectResponse
    {
        abort_unless($testDrive->car_id === $car->id, 404);
        $testDrive->delete();

        return redirect()
            ->route('admin.cars.test-drives.index', $car)
            ->with('success', 'Тест-драйв удален.');
    }
}
