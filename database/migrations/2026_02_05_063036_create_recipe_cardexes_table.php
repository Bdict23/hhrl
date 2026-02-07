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
        Schema::create('recipe_cardex', function (Blueprint $table) {
            $table->id();
           $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this cardex belongs to');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the item in the cardex');
            $table->decimal('qty_in', 10, 2)->default(0)->comment('Current quantity of the item received into the cardex');
            $table->decimal('qty_out', 10, 2)->default(0)->comment('Current quantity of the item issued from the cardex');
            $table->enum('status', ['TEMP', 'RESERVED', 'FINAL', 'CANCELLED'])->default('TEMP')->index('status');
            $table->enum('transaction_type', ['ADJUSTMENT', 'SALES'])->index('transaction_type');
            $table->foreignId('adjustment_id')->nullable()->default(null)->constrained('inventory_adjustments')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('order_id')->nullable()->default(null)->constrained('orders')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('final_date')->nullable()->index('final_date');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->index(['menu_id', 'branch_id'], 'idx_menu_branch'); // Index for faster search by department and branch
            $table->index('menu_id', 'idx_menu'); // Index for faster search by item and branch
            $table->index('branch_id', 'idx_branch');
        });

        // drop default qty in branch_menu_recipes
        Schema::table('branch_menu_recipes', function (Blueprint $table) {
            $table->dropColumn('default_qty');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_cardex');
    }
};
