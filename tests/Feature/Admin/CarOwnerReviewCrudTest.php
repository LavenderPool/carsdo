<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarOwnerReview;
use App\Models\MediaAlias;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarOwnerReviewCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_owner_review_admin(): void
    {
        [$car] = $this->createDependencies();

        $this->get(route('admin.cars.owner-reviews.index', $car))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_owner_reviews_with_photo(): void
    {
        if (!$this->supportsWebpEncoding()) {
            $this->markTestSkipped('WebP encoder is unavailable.');
        }

        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();
        [$car] = $this->createDependencies();

        $this->actingAs($user)
            ->post(route('admin.cars.owner-reviews.store', $car), [
                'import_index' => 0,
                'rating' => 5,
                'full_name' => 'Иван Петров',
                'photo' => $this->fakeImageUpload('ivan.png'),
                'text' => 'Отличный автомобиль для города.',
            ])
            ->assertRedirect(route('admin.cars.owner-reviews.index', $car));

        $review = CarOwnerReview::query()->firstOrFail();
        $originalPath = $review->photo_path;

        $this->assertSame(5, $review->rating);
        $this->assertSame('Иван Петров', $review->full_name);
        $this->assertTrue(Storage::disk('public')->exists((string) $originalPath));
        $this->assertDatabaseHas('media_aliases', [
            'owner_type' => CarOwnerReview::class,
            'owner_id' => $review->id,
            'source_path' => $originalPath,
            'variant' => 'webp',
        ]);

        $originalAliasPath = MediaAlias::query()
            ->where('owner_type', CarOwnerReview::class)
            ->where('owner_id', $review->id)
            ->value('alias_path');

        $this->assertNotNull($originalAliasPath);
        $this->assertTrue(Storage::disk('public')->exists((string) $originalAliasPath));

        $this->actingAs($user)
            ->put(route('admin.cars.owner-reviews.update', [$car, $review]), [
                'import_index' => 1,
                'rating' => 4,
                'full_name' => 'Петр Иванов',
                'photo' => $this->fakeImageUpload('petr.png'),
                'text' => 'Нравится комфорт и шумоизоляция.',
            ])
            ->assertRedirect(route('admin.cars.owner-reviews.index', $car));

        $review->refresh();

        $this->assertSame(1, $review->import_index);
        $this->assertSame(4, $review->rating);
        $this->assertSame('Петр Иванов', $review->full_name);
        $this->assertSame('Нравится комфорт и шумоизоляция.', $review->text);
        $this->assertNotSame($originalPath, $review->photo_path);
        $this->assertFalse(Storage::disk('public')->exists((string) $originalPath));
        $this->assertTrue(Storage::disk('public')->exists((string) $review->photo_path));
        $this->assertFalse(Storage::disk('public')->exists((string) $originalAliasPath));

        $updatedAliasPath = MediaAlias::query()
            ->where('owner_type', CarOwnerReview::class)
            ->where('owner_id', $review->id)
            ->value('alias_path');

        $this->assertNotNull($updatedAliasPath);
        $this->assertTrue(Storage::disk('public')->exists((string) $updatedAliasPath));

        $updatedPath = $review->photo_path;

        $this->actingAs($user)
            ->delete(route('admin.cars.owner-reviews.destroy', [$car, $review]))
            ->assertRedirect(route('admin.cars.owner-reviews.index', $car));

        $this->assertDatabaseCount('car_owner_reviews', 0);
        $this->assertDatabaseCount('media_aliases', 0);
        $this->assertFalse(Storage::disk('public')->exists((string) $updatedPath));
        $this->assertFalse(Storage::disk('public')->exists((string) $updatedAliasPath));
    }

    public function test_owner_review_validates_rating_and_photo(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();
        [$car] = $this->createDependencies();

        $this->actingAs($user)
            ->from(route('admin.cars.owner-reviews.create', $car))
            ->post(route('admin.cars.owner-reviews.store', $car), [
                'import_index' => 0,
                'rating' => 6,
                'full_name' => '',
                'photo' => UploadedFile::fake()->create('owner.txt', 10, 'text/plain'),
                'text' => '',
            ])
            ->assertRedirect(route('admin.cars.owner-reviews.create', $car))
            ->assertSessionHasErrors(['rating', 'full_name', 'photo', 'text']);

        $this->assertDatabaseCount('car_owner_reviews', 0);
    }

    private function createDependencies(): array
    {
        $brand = Brand::query()->create([
            'name' => 'Tesla',
            'slug' => 'tesla',
            'leave_from_russian' => false,
        ]);

        $car = Car::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Model Y',
            'slug' => 'model-y',
            'year' => '2024',
            'is_electric_car' => true,
            'is_soon' => false,
            'is_another_models' => false,
        ]);

        return [$car, $brand];
    }

    private function fakeImageUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            $this->fakeImageBinary(),
        );
    }

    private function fakeImageBinary(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAIAAAAmkwkpAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAEElEQVQImWP8z4AATAxEcQAz0QEH1mUzKgAAAABJRU5ErkJggg==',
            true,
        ) ?: '';
    }

    private function supportsWebpEncoding(): bool
    {
        return function_exists('imagewebp') || class_exists('\\Imagick');
    }
}
