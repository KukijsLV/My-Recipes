<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'newest');
        $allowedSorts = ['newest', 'oldest'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'newest';
        $user = $request->user();

        $recipes = Recipe::query()
            ->with('user')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->when($user, function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('visibility', 'public')
                        ->orWhere('user_id', $user->id);
                });
            }, function ($query) {
                $query->where('visibility', 'public');
            })
            ->when($search !== '', function ($query) use ($search) {
                $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
                $query->where(function ($q) use ($escapedSearch) {
                    $q->whereRaw('title LIKE ? ESCAPE CHAR(92)', ["%{$escapedSearch}%"])
                        ->orWhereRaw('description LIKE ? ESCAPE CHAR(92)', ["%{$escapedSearch}%"])
                        ->orWhereRaw('ingredients LIKE ? ESCAPE CHAR(92)', ["%{$escapedSearch}%"]);
                });
            })
            ->when($sort === 'oldest', function ($query) {
                $query->oldest();
            }, function ($query) {
                $query->latest();
            })
            ->paginate(12)
            ->withQueryString();

        return view('recipes.index', compact('recipes', 'search', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('recipes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'ingredients' => ['required', 'string', 'max:5000'],
            'instructions' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'visibility' => ['required', 'in:public,private'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
        } elseif ($validated['image_url'] ?? null) {
            $imagePath = $validated['image_url'];
        }

        $request->user()->recipes()->create([
            ...$validated,
            'image' => $imagePath,
        ]);
        unset($validated['image_url']);

        return to_route('recipes.index')->with('status', 'Recepte izveidota.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Recipe $recipe): View
    {
        Gate::authorize('view', $recipe);

        $recipe->loadAvg('ratings', 'rating');
        $recipe->loadCount('ratings');
        $userRating = $request->user()
            ? $recipe->ratings()->where('user_id', $request->user()->id)->value('rating')
            : null;

        return view('recipes.show', compact('recipe', 'userRating'));
    }

    /**
     * Store or update the authenticated user's rating for the recipe.
     */
    public function rate(Request $request, Recipe $recipe): RedirectResponse
    {
        Gate::authorize('view', $recipe);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $recipe->ratings()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['rating' => $validated['rating']],
        );

        return to_route('recipes.show', $recipe)->with('status', 'Paldies par vērtējumu!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe): View
    {
        Gate::authorize('update', $recipe);

        return view('recipes.edit', compact('recipe'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        Gate::authorize('update', $recipe);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'ingredients' => ['required', 'string', 'max:5000'],
            'instructions' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'visibility' => ['required', 'in:public,private'],
        ]);

        $oldImage = $recipe->image;
        $previousImageWasLocal = $this->isLocalRecipeImage($oldImage);
        $newImage = $recipe->image;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('recipes', 'public');
        } elseif ($request->filled('image_url')) {
            $newImage = $validated['image_url'];
        } elseif ($request->has('image_url')) {
            $newImage = null;
        }

        if ($newImage !== $oldImage && $previousImageWasLocal) {
            Storage::disk('public')->delete($oldImage);
        }

        $validated['image'] = $newImage;
        unset($validated['image_url']);
        $recipe->update($validated);

        return to_route('recipes.show', $recipe)->with('status', 'Recepte atjaunināta.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe): RedirectResponse
    {
        Gate::authorize('delete', $recipe);

        if ($this->isLocalRecipeImage($recipe->image)) {
            Storage::disk('public')->delete($recipe->image);
        }

        $recipe->delete();

        return to_route('recipes.index')->with('status', 'Recepte dzēsta.');
    }

    private function isLocalRecipeImage(?string $image): bool
    {
        return $image !== null
            && ! filter_var($image, FILTER_VALIDATE_URL)
            && ! str_starts_with($image, 'data:');
    }
}
