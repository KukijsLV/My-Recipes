<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeImageLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_recipe_removes_its_local_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $path = $user->recipes()->create([
            'title' => 'Image recipe',
            'ingredients' => 'Ingredient',
            'instructions' => 'Instruction',
            'visibility' => 'private',
            'image' => 'recipes/image.jpg',
        ]);
        Storage::disk('public')->put($path->image, 'image-data');

        $this->actingAs($user)->delete(route('recipes.destroy', $path))->assertRedirect();

        Storage::disk('public')->assertMissing($path->image);
    }

    public function test_replacing_a_local_image_removes_the_old_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldPath = 'recipes/old.jpg';
        Storage::disk('public')->put($oldPath, 'old-data');
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'image' => $oldPath,
        ]);

        $this->actingAs($user)
            ->put(route('recipes.update', $recipe), [
                'title' => $recipe->title,
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'visibility' => $recipe->visibility,
                'image' => UploadedFile::fake()->image('new.jpg'),
            ])
            ->assertRedirect();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($recipe->fresh()->image);
    }

    public function test_replacing_a_local_image_with_url_removes_the_old_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldPath = 'recipes/old.jpg';
        Storage::disk('public')->put($oldPath, 'old-data');
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'image' => $oldPath,
        ]);

        $this->actingAs($user)
            ->put(route('recipes.update', $recipe), [
                'title' => $recipe->title,
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'visibility' => $recipe->visibility,
                'image_url' => 'https://example.test/image.jpg',
            ])
            ->assertRedirect();

        Storage::disk('public')->assertMissing($oldPath);
        $this->assertSame('https://example.test/image.jpg', $recipe->fresh()->image);
    }

    public function test_invalid_image_url_and_excessive_text_are_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('recipes.update', $recipe), [
                'title' => $recipe->title,
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'visibility' => $recipe->visibility,
                'image_url' => 'not-a-url',
            ])
            ->assertSessionHasErrors(['image_url']);

        $this->actingAs($user)
            ->put(route('recipes.update', $recipe), [
                'title' => $recipe->title,
                'description' => str_repeat('x', 1001),
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'visibility' => $recipe->visibility,
            ])
            ->assertSessionHasErrors(['description']);
    }
}
