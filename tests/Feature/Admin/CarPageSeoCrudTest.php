<?php

namespace Tests\Feature\Admin;

use App\Models\CarPageSeo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarPageSeoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_car_page_seo_admin(): void
    {
        $this->get(route('admin.car-page-seos.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_and_update_car_page_seo_records(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.car-page-seos.index'))
            ->assertOk();

        $this->assertDatabaseCount('car_page_seos', 7);

        $page = CarPageSeo::query()->where('page_key', 'car_dealer')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.car-page-seos.edit', 'car_dealer'))
            ->assertOk();

        $this->actingAs($user)
            ->put(route('admin.car-page-seos.update', 'car_dealer'), [
                'title' => 'Дилеры {brand} {car}',
                'description' => 'Описание дилеров {city}',
                'h1' => 'Купить {brand} {car} в {city}',
                'og_image' => '/images/dealer-page.jpg',
                'canonical_url' => '/seo/dealers/',
                'robots' => 'index, follow',
            ])
            ->assertRedirect(route('admin.car-page-seos.edit', 'car_dealer'));

        $page->refresh();

        $this->assertSame('Дилеры {brand} {car}', $page->title);
        $this->assertSame('Описание дилеров {city}', $page->description);
        $this->assertSame('Купить {brand} {car} в {city}', $page->h1);
    }
}
