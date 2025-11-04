<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Recipe;

class RecipeController extends BaseController
{
    /**
     * Display a listing of recipes for the index view.
     */
    public function index()
    {
        $recipes = Recipe::orderBy('created_at', 'desc')->get();
        return view('index', compact('recipes'));
    }

    /**
     * Return a single recipe as JSON.
     */
    public function show($id)
    {
        $recipe = Recipe::findOrFail($id);
        return response()->json($recipe);
    }

    /**
     * Persist a recipe to the database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'servings' => 'nullable|string|max:50',
            'prep' => 'nullable|numeric',
            'cook' => 'nullable|numeric',
            'ingredients' => 'nullable|array',
            'instructions' => 'nullable|array',
        ]);

        // Normalize numeric fields
        if (isset($data['prep'])) $data['prep'] = (int)$data['prep'];
        if (isset($data['cook'])) $data['cook'] = (int)$data['cook'];

        // Ensure arrays exist
        $data['ingredients'] = $data['ingredients'] ?? [];
        $data['instructions'] = $data['instructions'] ?? [];

        $recipe = Recipe::create($data);

        return response()->json($recipe, 201);
    }

    /**
     * Update an existing recipe.
     */
    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'servings' => 'nullable|string|max:50',
            'prep' => 'nullable|numeric',
            'cook' => 'nullable|numeric',
            'ingredients' => 'nullable|array',
            'instructions' => 'nullable|array',
        ]);

        if (isset($data['prep'])) $data['prep'] = (int)$data['prep'];
        if (isset($data['cook'])) $data['cook'] = (int)$data['cook'];

        $data['ingredients'] = $data['ingredients'] ?? [];
        $data['instructions'] = $data['instructions'] ?? [];

        $recipe->update($data);

        return response()->json($recipe);
    }

    /**
     * Delete a recipe.
     */
    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();
        return response()->json(['deleted' => true]);
    }
}
