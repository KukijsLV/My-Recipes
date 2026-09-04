<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $recipes = $request->user()->recipes()->latest()->get();

        return view('recipes.index', compact('recipes'));
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
            'description' => ['nullable', 'string'],
            'ingredients' => ['required', 'string'],
            'instructions' => ['required', 'string'],
            'image' => ['nullable', 'url', 'max:2048'],
            'visibility' => ['required', 'in:public,private'],
        ]);

        $request->user()->recipes()->create($validated);

        return to_route('recipes.index')->with('status', 'Recepte izveidota.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe): View
    {
        Gate::authorize('view', $recipe);

        return view('recipes.show', compact('recipe'));
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
            'description' => ['nullable', 'string'],
            'ingredients' => ['required', 'string'],
            'instructions' => ['required', 'string'],
            'image' => ['nullable', 'url', 'max:2048'],
            'visibility' => ['required', 'in:public,private'],
        ]);

        $recipe->update($validated);

        return to_route('recipes.show', $recipe)->with('status', 'Recepte atjaunināta.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe): RedirectResponse
    {
        Gate::authorize('delete', $recipe);
        $recipe->delete();

        return to_route('recipes.index')->with('status', 'Recepte dzēsta.');
    }
}
