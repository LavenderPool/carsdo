<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarCrashTestRequest;
use App\Http\Requests\Admin\UpdateCarCrashTestRequest;
use App\Models\Car;
use App\Models\CarCrashTest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarCrashTestController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->crashTest()
            ->get()
            ->map(fn (CarCrashTest $item) => [
                'id' => $item->id,
                'year' => $item->year,
                'rating' => $item->rating,
                'video_path' => $item->video_path,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Краш-тесты',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'year', 'label' => 'Год'],
                ['key' => 'rating', 'label' => 'Рейтинг'],
                ['key' => 'video_path', 'label' => 'Видео'],
            ],
            'createUrl' => route('admin.cars.crash-tests.create', $car),
            'editBaseUrl' => route('admin.cars.crash-tests.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.crash-tests.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить краш-тест',
            'emptyMessage' => 'Краш-тесты пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить запись краш-теста #{id}?',
        ]);
    }

    public function create(Car $car): Response|RedirectResponse
    {
        $existingCrashTest = $car->crashTest()->first();

        if ($existingCrashTest !== null) {
            return redirect()
                ->route('admin.cars.crash-tests.edit', [$car, $existingCrashTest])
                ->with('success', 'У этой машины уже есть краш-тест. Открыто редактирование.');
        }

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новый краш-тест',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.crash-tests.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.crash-tests.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'year' => null,
                'rating' => null,
                'video_path' => '',
            ],
            'fields' => [
                ['name' => 'year', 'label' => 'Год', 'type' => 'number'],
                ['name' => 'rating', 'label' => 'Рейтинг', 'type' => 'number'],
                ['name' => 'video_path', 'label' => 'Ссылка на видео', 'type' => 'text'],
            ],
        ]);
    }

    public function store(StoreCarCrashTestRequest $request, Car $car): RedirectResponse
    {
        if ($car->crashTest()->exists()) {
            return redirect()
                ->route('admin.cars.crash-tests.index', $car)
                ->with('success', 'Краш-тест уже существует. Можно только редактировать существующую запись.');
        }

        $car->crashTest()->create($request->validated());

        return redirect()
            ->route('admin.cars.crash-tests.index', $car)
            ->with('success', 'Краш-тест добавлен.');
    }

    public function edit(Car $car, CarCrashTest $crashTest): Response
    {
        abort_unless($crashTest->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование краш-теста',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.crash-tests.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.crash-tests.update', [$car, $crashTest]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'year' => $crashTest->year,
                'rating' => $crashTest->rating,
                'video_path' => $crashTest->video_path,
            ],
            'fields' => [
                ['name' => 'year', 'label' => 'Год', 'type' => 'number'],
                ['name' => 'rating', 'label' => 'Рейтинг', 'type' => 'number'],
                ['name' => 'video_path', 'label' => 'Ссылка на видео', 'type' => 'text'],
            ],
        ]);
    }

    public function update(UpdateCarCrashTestRequest $request, Car $car, CarCrashTest $crashTest): RedirectResponse
    {
        abort_unless($crashTest->car_id === $car->id, 404);
        $crashTest->update($request->validated());

        return redirect()
            ->route('admin.cars.crash-tests.index', $car)
            ->with('success', 'Краш-тест обновлен.');
    }

    public function destroy(Car $car, CarCrashTest $crashTest): RedirectResponse
    {
        abort_unless($crashTest->car_id === $car->id, 404);
        $crashTest->delete();

        return redirect()
            ->route('admin.cars.crash-tests.index', $car)
            ->with('success', 'Краш-тест удален.');
    }
}
