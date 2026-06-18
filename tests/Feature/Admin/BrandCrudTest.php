<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $response = $this->get(route('admin.brands.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_brands(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.brands.store'), [
                'name' => 'BMW',
                'slug' => '',
                'leave_from_russian' => true,
                'seo_title' => '{brand} title',
                'seo_description' => 'Brand description',
                'seo_h1' => 'Brand H1',
                'seo_og_image' => '/images/brand.jpg',
                'seo_canonical_url' => '/brands/custom/',
                'seo_robots' => 'index, follow',
            ])
            ->assertRedirect(route('admin.brands.index'));

        $brand = Brand::query()->firstOrFail();

        $this->assertSame('BMW', $brand->name);
        $this->assertSame('bmw', $brand->slug);
        $this->assertTrue($brand->leave_from_russian);
        $this->assertSame('{brand} title', $brand->seo_title);

        $this->actingAs($user)
            ->put(route('admin.brands.update', $brand), [
                'name' => 'Audi',
                'slug' => 'audi-brand',
                'leave_from_russian' => false,
                'seo_title' => 'Audi title',
                'seo_description' => 'Updated description',
                'seo_h1' => 'Audi H1',
                'seo_og_image' => '/images/audi.jpg',
                'seo_canonical_url' => '/brands/audi/',
                'seo_robots' => 'noindex, nofollow',
            ])
            ->assertRedirect(route('admin.brands.index'));

        $brand->refresh();

        $this->assertSame('Audi', $brand->name);
        $this->assertSame('audi-brand', $brand->slug);
        $this->assertFalse($brand->leave_from_russian);
        $this->assertSame('Audi title', $brand->seo_title);
        $this->assertSame('noindex, nofollow', $brand->seo_robots);

        $this->actingAs($user)
            ->delete(route('admin.brands.destroy', $brand))
            ->assertRedirect(route('admin.brands.index'));

        $this->assertSoftDeleted($brand);
    }
}
