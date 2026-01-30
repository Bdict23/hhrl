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
        Schema::create('item_waste_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('cancellation_id')->nullable()->constrained('cancellation_of_order_logs')->onDelete('no action')->onUpdate('no action');
            $table->decimal('waste_cost', 15, 2);
            $table->foreignId('price_level_cost')->nullable()->constrained('price_levels')->onDelete('no action')->onUpdate('no action');
            $table->decimal('waste_selling_price', 15, 2);
            $table->foreignId('price_level_srp')->nullable()->constrained('price_levels')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            
        });
        Schema::table('cancellation_of_order_logs', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('no action')->onUpdate('no action');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('served_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_waste_lgos');
    }
};
