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
        if (Schema::hasTable('recipes')) {
            Schema::table('recipes', function (Blueprint $table) {
                if (!Schema::hasColumn('recipes', 'description')) {
                    $table->text('description')->nullable()->after('name');
                }
                if (!Schema::hasColumn('recipes', 'category')) {
                    $table->string('category')->nullable()->after('description');
                }
                if (!Schema::hasColumn('recipes', 'servings')) {
                    $table->string('servings')->nullable()->after('category');
                }
                if (!Schema::hasColumn('recipes', 'prep')) {
                    $table->integer('prep')->nullable()->after('servings');
                }
                if (!Schema::hasColumn('recipes', 'cook')) {
                    $table->integer('cook')->nullable()->after('prep');
                }
                if (!Schema::hasColumn('recipes', 'ingredients')) {
                    $table->json('ingredients')->nullable()->after('cook');
                }
                if (!Schema::hasColumn('recipes', 'instructions')) {
                    $table->json('instructions')->nullable()->after('ingredients');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recipes')) {
            Schema::table('recipes', function (Blueprint $table) {
                if (Schema::hasColumn('recipes', 'instructions')) $table->dropColumn('instructions');
                if (Schema::hasColumn('recipes', 'ingredients')) $table->dropColumn('ingredients');
                if (Schema::hasColumn('recipes', 'cook')) $table->dropColumn('cook');
                if (Schema::hasColumn('recipes', 'prep')) $table->dropColumn('prep');
                if (Schema::hasColumn('recipes', 'servings')) $table->dropColumn('servings');
                if (Schema::hasColumn('recipes', 'category')) $table->dropColumn('category');
                if (Schema::hasColumn('recipes', 'description')) $table->dropColumn('description');
            });
        }
    }
};
