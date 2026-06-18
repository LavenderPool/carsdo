<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_settings_page_is_available_for_authenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.settings.edit'))
            ->assertOk();

        $this->assertDatabaseHas('settings', [
            'id' => 1,
            'brand_name' => 'carsDo',
        ]);
    }

    public function test_authenticated_user_can_update_brand_name_and_favicon(): void
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('admin.settings.update'), [
                'brand_name' => 'My Cars',
                'favicon' => UploadedFile::fake()->image('favicon.png', 64, 64),
                'seo_title_suffix' => '| My Cars',
                'seo_default_description' => 'Default site description',
                'seo_default_robots' => 'index, follow',
                'seo_default_og_image' => '/images/default.jpg',
                'home_seo_title' => 'Home {site_name}',
                'home_seo_description' => 'Home description',
                'home_seo_h1' => 'Home H1',
                'new_cars_seo_title' => 'Cars {year}',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $setting = Setting::query()->firstOrFail();

        $this->assertSame('My Cars', $setting->brand_name);
        $this->assertNotNull($setting->favicon_path);
        $this->assertSame('| My Cars', $setting->seo_title_suffix);
        $this->assertSame('Home H1', $setting->home_seo_h1);
        Storage::disk('public')->assertExists($setting->favicon_path);
    }
}
