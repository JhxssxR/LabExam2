<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'category', 'servings', 'prep', 'cook', 'ingredients', 'instructions'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'prep' => 'integer',
        'cook' => 'integer',
    ];
}
