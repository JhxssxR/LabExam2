<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('recipes')) {
            Schema::create('recipes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('category')->nullable();
                $table->string('servings')->nullable();
                $table->integer('prep')->nullable();
                $table->integer('cook')->nullable();
                $table->json('ingredients')->nullable();
                $table->json('instructions')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
