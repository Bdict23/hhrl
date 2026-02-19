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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['DRAFT','PENDING', 'CANCELLED', 'COMPLETED'])->default('DRAFT');
            $table->foreignId('prepared_by')->constrained('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->index('branch_id', 'idx_branch');
            $table->index('status', 'idx_status');
            $table->index('prepared_by', 'idx_prepared_by');
            $table->index('created_at', 'idx_created_at');
            $table->index('updated_at', 'idx_updated_at');
            $table->index(['branch_id', 'status'], 'idx_branch_status');
            });

         Schema::create('production_order_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('production_order_id')->constrained('production_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('qty');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->index('branch_id', 'idx_branch');
            $table->index('production_order_id', 'idx_production_order_id');
            $table->index('menu_id', 'idx_menu');
        });
       
        
       
       
            Schema::create('production_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('qty', 15, 2);
            $table->enum('status', ['PENDING', 'CANCELLED', 'COMPLETED'])->default('PENDING');
            $table->foreignId('uom_id')->constrained('unit_of_measures')->onDelete('no action')->onUpdate('cascade');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->index('production_order_id', 'idx_production_order');
            $table->index('item_id', 'idx_item');
            $table->index('status', 'idx_status');
            $table->index('uom_id', 'idx_uom');
            $table->index('menu_id', 'idx_menu');
        });

        // add production order id on withdrawal table
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreignId('production_order_id')->nullable()->constrained('production_orders')->onDelete('set null')->onUpdate('cascade');
            $table->index('production_order_id', 'idx_production_order_id');
        });

        // add production order id on recipe cardex table
        Schema::table('recipe_cardex', function (Blueprint $table) {
            $table->foreignId('production_order_id')->nullable()->constrained('production_orders')->onDelete('set null')->onUpdate('cascade');
            $table->index('production_order_id', 'idx_production_order_id');
        });
        //add PRODUCTION enum on transaction type on recipe cardex table
        Schema::table('recipe_cardex', function (Blueprint $table) {
            $table->enum('transaction_type', ['PRODUCTION', 'ADJUSTMENT', 'SALES'])->default('ADJUSTMENT')->change();
            $table->index('transaction_type', 'idx_transaction_type');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['production_order_id']);
            $table->dropIndex('idx_production_order_id');
            $table->dropColumn('production_order_id');
        });
        Schema::dropIfExists('production_order_details');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('production_order_menus');
    }
};
