<?php

namespace Tests\Feature\Admin;

use App\Models\Dealer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dealer_admin(): void
    {
        $this->get(route('admin.dealers.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_dealers(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.dealers.store'), [
                'name' => 'Tesla Store',
            ])
            ->assertRedirect(route('admin.dealers.index'));

        $dealer = Dealer::query()->firstOrFail();

        $this->assertSame('Tesla Store', $dealer->name);

        $this->actingAs($user)
            ->put(route('admin.dealers.update', $dealer), [
                'name' => 'BMW Store',
            ])
            ->assertRedirect(route('admin.dealers.index'));

        $dealer->refresh();

        $this->assertSame('BMW Store', $dealer->name);

        $this->actingAs($user)
            ->delete(route('admin.dealers.destroy', $dealer))
            ->assertRedirect(route('admin.dealers.index'));

        $this->assertDatabaseCount('dealers', 0);
    }
}
