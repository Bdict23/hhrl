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
        //
         Schema::table('branch_menu_recipes', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_menu_recipes', 'default_qty')) {
                $table->integer('default_qty')->default(0)->after('menu_id');
            }
        });

        Schema::table('branch_menu_recipes', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_menu_recipes', 'bal_qty')) {
                $table->integer('bal_qty')->default(0)->after('menu_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
            Schema::table('branch_menu_recipes', function (Blueprint $table) {
                if (Schema::hasColumn('branch_menu_recipes', 'default_qty')) {
                    $table->dropColumn('default_qty');
                }
            });
        Schema::table('branch_menu_recipes', function (Blueprint $table) {
            if (Schema::hasColumn('branch_menu_recipes', 'bal_qty')) {
                $table->dropColumn('bal_qty');
            }
        });
    }
};
