<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'ingredients' => fake()->paragraphs(2, true),
            'instructions' => fake()->paragraphs(2, true),
            'image' => fake()->optional()->imageUrl(),
            'visibility' => fake()->randomElement(['public', 'private']),
        ];
    }
}
