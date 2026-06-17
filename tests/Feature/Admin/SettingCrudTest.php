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
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $setting = Setting::query()->firstOrFail();

        $this->assertSame('My Cars', $setting->brand_name);
        $this->assertNotNull($setting->favicon_path);
        Storage::disk('public')->assertExists($setting->favicon_path);
    }
}
