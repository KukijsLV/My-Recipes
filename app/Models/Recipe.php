<?php

namespace App\Models;

use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'ingredients', 'instructions', 'image', 'color', 'visibility'])]
class Recipe extends Model
{
    /** @use HasFactory<RecipeFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $recipe): void {
            $recipe->color ??= self::randomColor();
        });
    }

    public static function randomColor(): string
    {
        $palette = [
            '#F59E0B',
            '#10B981',
            '#3B82F6',
            '#EF4444',
            '#8B5CF6',
            '#EC4899',
            '#14B8A6',
            '#F97316',
            '#84CC16',
            '#E11D48',
        ];

        return $palette[array_rand($palette)];
    }

    /**
     * Get the recipe owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
