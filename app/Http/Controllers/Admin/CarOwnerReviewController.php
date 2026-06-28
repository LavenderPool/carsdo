<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarOwnerReviewRequest;
use App\Http\Requests\Admin\UpdateCarOwnerReviewRequest;
use App\Models\Car;
use App\Models\CarOwnerReview;
use App\Support\Media\CarMediaStorage;
use App\Support\Media\MediaVariantService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarOwnerReviewController extends Controller
{
    public function index(Car $car): Response
    {
        $items = $car->ownerReviews()
            ->latest()
            ->get()
            ->map(fn (CarOwnerReview $item) => [
                'id' => $item->id,
                'name' => $item->full_name,
                'import_index' => $item->import_index,
                'rating' => $item->rating,
                'full_name' => $item->full_name,
                'text' => $item->text,
                'preview_url' => $item->photoUrl(),
            ]);

        return Inertia::render('Admin/Cars/Nested/Index', [
            'title' => 'Отзывы владельцев',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'items' => $items,
            'columns' => [
                ['key' => 'preview_url', 'label' => 'Фото', 'type' => 'image'],
                ['key' => 'rating', 'label' => 'Рейтинг'],
                ['key' => 'full_name', 'label' => 'ФИО'],
                ['key' => 'text', 'label' => 'Текст'],
            ],
            'createUrl' => route('admin.cars.owner-reviews.create', $car),
            'editBaseUrl' => route('admin.cars.owner-reviews.edit', [$car, '__ID__']),
            'destroyBaseUrl' => route('admin.cars.owner-reviews.destroy', [$car, '__ID__']),
            'backUrl' => route('admin.cars.edit', $car),
            'createLabel' => 'Добавить отзыв владельца',
            'emptyMessage' => 'Отзывы владельцев пока не добавлены.',
            'deleteMessageTemplate' => 'Удалить отзыв "{name}"?',
        ]);
    }

    public function create(Car $car): Response
    {
        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Новый отзыв владельца',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.owner-reviews.index', $car),
            'submit' => [
                'method' => 'post',
                'url' => route('admin.cars.owner-reviews.store', $car),
                'label' => 'Создать',
            ],
            'item' => [
                'import_index' => null,
                'rating' => 5,
                'full_name' => '',
                'photo' => null,
                'text' => '',
            ],
            'fields' => [
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'rating', 'label' => 'Рейтинг', 'type' => 'select', 'required' => true, 'options' => $this->ratingOptions()],
                ['name' => 'full_name', 'label' => 'ФИО', 'type' => 'text', 'required' => true],
                ['name' => 'photo', 'label' => 'Фото', 'type' => 'file', 'accept' => 'image/*'],
                ['name' => 'text', 'label' => 'Текст отзыва', 'type' => 'textarea', 'required' => true],
            ],
        ]);
    }

    public function store(StoreCarOwnerReviewRequest $request, Car $car): RedirectResponse
    {
        $review = $car->ownerReviews()->create($this->attributesFromRequest($request, $car));

        if (filled($review->photo_path)) {
            app(MediaVariantService::class)->ensureWebpVariant($review->photo_path, CarOwnerReview::class, $review->id);
        }

        return redirect()
            ->route('admin.cars.owner-reviews.index', $car)
            ->with('success', 'Отзыв владельца добавлен.');
    }

    public function edit(Car $car, CarOwnerReview $ownerReview): Response
    {
        abort_unless($ownerReview->car_id === $car->id, 404);

        return Inertia::render('Admin/Cars/Nested/Form', [
            'title' => 'Редактирование отзыва владельца',
            'car' => ['id' => $car->id, 'name' => $car->name],
            'backUrl' => route('admin.cars.owner-reviews.index', $car),
            'submit' => [
                'method' => 'put',
                'url' => route('admin.cars.owner-reviews.update', [$car, $ownerReview]),
                'label' => 'Сохранить',
            ],
            'item' => [
                'import_index' => $ownerReview->import_index,
                'rating' => $ownerReview->rating,
                'full_name' => $ownerReview->full_name,
                'photo' => null,
                'photo_url' => $ownerReview->originalPhotoUrl(),
                'text' => $ownerReview->text,
            ],
            'fields' => [
                ['name' => 'import_index', 'label' => 'Import index', 'type' => 'number'],
                ['name' => 'rating', 'label' => 'Рейтинг', 'type' => 'select', 'required' => true, 'options' => $this->ratingOptions()],
                ['name' => 'full_name', 'label' => 'ФИО', 'type' => 'text', 'required' => true],
                ['name' => 'photo', 'label' => 'Новое фото', 'type' => 'file', 'accept' => 'image/*'],
                ['name' => 'text', 'label' => 'Текст отзыва', 'type' => 'textarea', 'required' => true],
            ],
        ]);
    }

    public function update(UpdateCarOwnerReviewRequest $request, Car $car, CarOwnerReview $ownerReview): RedirectResponse
    {
        abort_unless($ownerReview->car_id === $car->id, 404);

        if ($request->hasFile('photo')) {
            CarMediaStorage::deleteOwnerReviewPhoto($ownerReview);
        }

        $ownerReview->update($this->attributesFromRequest($request, $car, $ownerReview));

        if (filled($ownerReview->photo_path)) {
            app(MediaVariantService::class)->ensureWebpVariant($ownerReview->photo_path, CarOwnerReview::class, $ownerReview->id);
        }

        return redirect()
            ->route('admin.cars.owner-reviews.index', $car)
            ->with('success', 'Отзыв владельца обновлен.');
    }

    public function destroy(Car $car, CarOwnerReview $ownerReview): RedirectResponse
    {
        abort_unless($ownerReview->car_id === $car->id, 404);

        CarMediaStorage::deleteOwnerReviewPhoto($ownerReview);
        CarOwnerReview::query()->whereKey($ownerReview->id)->delete();

        return redirect()
            ->route('admin.cars.owner-reviews.index', $car)
            ->with('success', 'Отзыв владельца удален.');
    }

    private function attributesFromRequest(
        StoreCarOwnerReviewRequest|UpdateCarOwnerReviewRequest $request,
        Car $car,
        ?CarOwnerReview $ownerReview = null,
    ): array {
        $data = $request->safe()->except(['photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storeUploadedPhoto($request, $car);
        } elseif ($ownerReview !== null) {
            $data['photo_path'] = $ownerReview->photo_path;
        }

        return $data;
    }

    private function storeUploadedPhoto(StoreCarOwnerReviewRequest|UpdateCarOwnerReviewRequest $request, Car $car): string
    {
        return $request->file('photo')->store(
            'images/'.$car->brand->slug.'/'.$car->slug.'/reviews',
            'public',
        );
    }

    private function ratingOptions(): array
    {
        return collect(range(1, 5))
            ->map(fn (int $rating) => ['value' => $rating, 'label' => (string) $rating])
            ->all();
    }
}
