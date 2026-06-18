<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarReviewRequest;
use App\Http\Requests\Admin\UpdateCarReviewRequest;
use App\Models\Car;
use App\Models\CarReview;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarReviewController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->reviews()
            ->latest()
            ->get()
            ->map(fn (CarReview $item) => [
                'id' => $item->id,
                'import_index' => $item->import_index,
                'type' => $this->typeLabel($item->type),
                'value' => $item->value,
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Отзывы',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'import_index', 'label' => 'Import index'],
                ['key' => 'type', 'label' => 'Тип'],
                ['key' => 'value', 'label' => 'Текст'],
            ],
            'createUrl' => route('admin.cars.reviews.create', $car),
            'editBaseUrl' => route('admin.cars.reviews.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.reviews.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить отзыв',
            'emptyMessage' => 'Отзывы пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить отзыв #{id}?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новый отзыв',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.reviews.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.reviews.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'import_index' => null,
                'type' => 'good',
                'value' => '',
            ],
            'fields' => [
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'type', 'label' => 'Тип', 'type' => 'select', 'required' => true, 'options' => $this->typeOptions()],
                ['name' => 'value', 'label' => 'Текст', 'type' => 'textarea'],
            ],
        ]);
    }

    public function store(StoreCarReviewRequest $request, Car $car): RedirectResponse
    {
        $car->reviews()->create($request->validated());

        return redirect()
            ->route('admin.cars.reviews.index', $car)
            ->with('success', 'Отзыв добавлен.');
    }

    public function edit(Car $car, CarReview $review): Response
    {
        abort_unless($review->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование отзыва',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.reviews.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.reviews.update', [$car, $review]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'import_index' => $review->import_index,
                'type' => $review->type,
                'value' => $review->value,
            ],
            'fields' => [
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'type', 'label' => 'Тип', 'type' => 'select', 'required' => true, 'options' => $this->typeOptions()],
                ['name' => 'value', 'label' => 'Текст', 'type' => 'textarea'],
            ],
        ]);
    }

    public function update(UpdateCarReviewRequest $request, Car $car, CarReview $review): RedirectResponse
    {
        abort_unless($review->car_id === $car->id, 404);
        $review->update($request->validated());

        return redirect()
            ->route('admin.cars.reviews.index', $car)
            ->with('success', 'Отзыв обновлен.');
    }

    public function destroy(Car $car, CarReview $review): RedirectResponse
    {
        abort_unless($review->car_id === $car->id, 404);
        $review->delete();

        return redirect()
            ->route('admin.cars.reviews.index', $car)
            ->with('success', 'Отзыв удален.');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function typeOptions(): array
    {
        return [
            ['value' => 'good', 'label' => 'Преимущество'],
            ['value' => 'bad', 'label' => 'Недостаток'],
        ];
    }

    private function typeLabel(?string $type): string
    {
        return match ($type) {
            'good' => 'Преимущество',
            'bad' => 'Недостаток',
            default => $type ?? '-',
        };
    }
}
