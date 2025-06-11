<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('branch_menus', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('is_available');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->index('recipe_type');
            $table->index('status');
        });

        Schema::table('branch_menu_recipes', function (Blueprint $table) {
            $table->index('branch_menu_id');
            $table->index('menu_id');
        });
    }

    public function down()
    {
        Schema::table('branch_menus', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['is_available']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['recipe_type']);
            $table->dropIndex(['status']);
        });

        Schema::table('branch_menu_recipes', function (Blueprint $table) {
            $table->dropIndex(['branch_menu_id']);
            $table->dropIndex(['menu_id']);
        });
    }
};
