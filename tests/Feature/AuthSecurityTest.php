<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_other_user_profile_does_not_expose_private_recipe_data(): void
    {
        $owner = User::factory()->create();
        $privateRecipe = Recipe::factory()->create([
            'user_id' => $owner->id,
            'visibility' => 'private',
            'title' => 'Private title',
            'description' => 'Private description',
            'ingredients' => 'Private ingredient',
            'instructions' => 'Private instructions',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('user.profile', $owner))
            ->assertOk()
            ->assertDontSee($privateRecipe->title)
            ->assertDontSee('Private description')
            ->assertDontSee('Private ingredient')
            ->assertDontSee('Private instructions');
    }

    public function test_owner_profile_exposes_their_private_recipe(): void
    {
        $owner = User::factory()->create();
        Recipe::factory()->create([
            'user_id' => $owner->id,
            'visibility' => 'private',
            'title' => 'My private recipe',
        ]);

        $this->actingAs($owner)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('My private recipe');
    }

    public function test_blocked_authenticated_user_cannot_access_authenticated_functionality(): void
    {
        $user = User::factory()->create(['is_blocked' => true]);

        $this->actingAs($user)
            ->post(route('recipes.store'), [
                'title' => 'Blocked recipe',
                'ingredients' => 'Ingredient',
                'instructions' => 'Instruction',
                'visibility' => 'private',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->assertForbidden();
    }

    public function test_admin_cannot_block_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-block', $admin))
            ->assertForbidden();

        $admin->refresh();
        $this->assertFalse($admin->is_blocked);
    }

    public function test_last_active_admin_cannot_be_blocked(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-block', $admin))
            ->assertForbidden();

        $admin->refresh();
        $this->assertFalse($admin->is_blocked);
    }

    public function test_login_and_registration_routes_are_rate_limited_independently(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post(route('login.store'), [
                'email' => 'missing@example.com',
                'password' => 'wrong',
            ])
            ->assertSessionHasErrors(['email']);

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->post(route('login.store'), [
                    'email' => 'missing@example.com',
                    'password' => 'wrong',
                ]);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post(route('register.store'), [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->post(route('register.store'), [
                    'name' => 'Another User',
                    'email' => "attempt-{$attempt}@example.com",
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ]);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post(route('register.store'), [
                'name' => 'Blocked User',
                'email' => 'blocked@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertStatus(429);
    }
}
