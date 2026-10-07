<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_private_recipe_and_update_without_show_forbidden(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $recipe = Recipe::factory()->create([
            'user_id' => $owner->id,
            'visibility' => 'private',
            'title' => 'Admin private recipe',
        ]);

        $this->actingAs($admin)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Admin private recipe');

        $this->actingAs($admin)
            ->put(route('recipes.update', $recipe), [
                'title' => 'Updated by admin',
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'visibility' => 'private',
            ])
            ->assertRedirect(route('recipes.show', $recipe));

        $this->assertSame('Updated by admin', $recipe->fresh()->title);
    }

    public function test_admin_cannot_block_last_active_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-block', $admin))
            ->assertForbidden();
    }
}
