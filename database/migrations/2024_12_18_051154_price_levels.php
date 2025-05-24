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
        if (!Schema::hasTable('price_levels')) {
        Schema::create('price_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('no action')->onUpdate('no action');
            $table->string('price_type', 255)->nullable()->index('price_type_index')->comment('Type of price level, e.g., retail, wholesale');
            $table->integer('markup')->nullable();
            $table->decimal('amount', 19, 2)->nullable();
            $table->date('start_date')->nullable()->index('start_date_index')->comment('Start date for the price level');
            $table->date('end_date')->nullable()->index('end_date_index')->comment('End date for the price level');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change


        });}

         // Create requisition_details table
         if (!Schema::hasTable('requisition_details')) {
            Schema::create('requisition_details', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->onUpdate('cascade'); // Foreign key to items table
                $table->decimal('qty',4,2)->unsigned(); // Quantity
                $table->foreignId('price_level_id')->nullable()->constrained('price_levels')->onDelete('cascade')->onUpdate('cascade'); // Foreign key to price_levels
                $table->foreignId('requisition_info_id')->constrained('requisition_infos')->onDelete('cascade')->onUpdate('cascade'); // Foreign key to requisition_infos
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
