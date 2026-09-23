<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_recipe(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('recipes.create'))->assertOk();

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

    public function test_new_recipe_gets_a_random_color(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('recipes.store'), [
            'title' => 'Kartupeļu zupa',
            'description' => 'Garšīga zupa.',
            'ingredients' => 'Kartupeļi',
            'instructions' => 'Uzvārīt.',
            'visibility' => 'public',
        ]);

        $recipe = Recipe::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertNotNull($recipe->color);
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $recipe->color);
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

    public function test_public_recipes_are_visible_on_the_index_for_guests(): void
    {
        $author = User::factory()->create(['name' => 'Anna']);
        $recipe = Recipe::factory()->create([
            'user_id' => $author->id,
            'visibility' => 'public',
            'title' => 'Siera salāti',
        ]);

        $this->get(route('recipes.index'))
            ->assertOk()
            ->assertSee('Siera salāti')
            ->assertSee('Anna');
    }

    public function test_recipe_show_page_displays_the_author_name(): void
    {
        $author = User::factory()->create(['name' => 'Anna']);
        $recipe = Recipe::factory()->create([
            'user_id' => $author->id,
            'visibility' => 'public',
        ]);

        $this->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Anna');
    }

    public function test_profile_page_lists_users_recipes(): void
    {
        $user = User::factory()->create(['name' => 'Līga']);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'public',
            'title' => 'Miežu zupa',
        ]);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Miežu zupa')
            ->assertSee('Līga');
    }

    public function test_authenticated_user_can_search_recipes_by_keyword(): void
    {
        $user = User::factory()->create();
        $matchingRecipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Tomātu zupa',
            'ingredients' => 'tomāti, sīpoli',
        ]);
        Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Kartupeļu biezeni',
            'ingredients' => 'kartupeļi, piens',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.index', ['search' => 'tomāti']))
            ->assertOk()
            ->assertSee($matchingRecipe->title)
            ->assertDontSee('Kartupeļu biezeni');
    }

    public function test_recipe_ingredients_are_rendered_as_a_numbered_list(): void
    {
        $recipe = Recipe::factory()->create([
            'title' => 'Tomātu zupa',
            'ingredients' => "tomāti\nsīpoli\nūdens",
            'visibility' => 'public',
        ]);

        $this->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSeeInOrder(['<ol>', '<li>tomāti</li>', '<li>sīpoli</li>', '<li>ūdens</li>'], false);
    }

    public function test_authenticated_user_can_update_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Vecais vārds',
            'email' => 'vecais@example.com',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Jauns vārds',
                'email' => 'jauns@example.com',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
                'profile_image' => UploadedFile::fake()->image('profile.jpg'),
            ])
            ->assertRedirect(route('profile'));

        $user->refresh();

        $this->assertSame('Jauns vārds', $user->name);
        $this->assertSame('jauns@example.com', $user->email);
        $this->assertTrue(Hash::check('new-password-123', $user->password));
        $this->assertNotNull($user->profile_image);
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
