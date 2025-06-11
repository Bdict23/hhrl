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
        Schema::table('branch_menus', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropForeign(['combo_recipe_id']);
            $table->dropColumn(['menu_id', 'combo_recipe_id']);
        });
        Schema::create('branch_menu_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_menu_id')->constrained('branch_menus')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_menu_recipes');
    }
};
