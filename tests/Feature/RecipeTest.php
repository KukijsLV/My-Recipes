<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_recipe(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('recipes.store'), [
            'title' => 'Zupa',
            'description' => 'Silta zupa.',
            'ingredients' => 'Ūdens',
            'instructions' => 'Uzvārīt.',
            'visibility' => 'private',
        ]);

        $response->assertRedirect(route('recipes.index'));
        $this->assertDatabaseHas('recipes', ['user_id' => $user->id, 'title' => 'Zupa']);
    }

    public function test_private_recipe_is_hidden_from_another_user(): void
    {
        $owner = User::factory()->create();
        $recipe = Recipe::factory()->create(['user_id' => $owner->id, 'visibility' => 'private']);

        $this->actingAs(User::factory()->create())->get(route('recipes.show', $recipe))->assertForbidden();
    }

    public function test_public_recipe_can_be_viewed_by_a_guest(): void
    {
        $recipe = Recipe::factory()->create(['visibility' => 'public']);

        $this->get(route('recipes.show', $recipe))->assertOk()->assertSee($recipe->title);
    }

    public function test_user_cannot_update_or_delete_another_users_recipe(): void
    {
        $recipe = Recipe::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)->put(route('recipes.update', $recipe), [
            'title' => 'Mainīts',
            'ingredients' => 'Ūdens',
            'instructions' => 'Uzvārīt.',
            'visibility' => 'public',
        ])->assertForbidden();

        $this->actingAs($otherUser)->delete(route('recipes.destroy', $recipe))->assertForbidden();
    }
}
