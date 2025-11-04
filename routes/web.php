<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RecipeController;

// Show index and the saved recipes
Route::get('/', [RecipeController::class, 'index']);

// Simple endpoint to accept recipe submissions from the frontend modal
Route::post('/recipes', [RecipeController::class, 'store']);

// recipe REST endpoints for show/update/delete
Route::get('/recipes/{id}', [RecipeController::class, 'show']);
Route::put('/recipes/{id}', [RecipeController::class, 'update']);
Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
