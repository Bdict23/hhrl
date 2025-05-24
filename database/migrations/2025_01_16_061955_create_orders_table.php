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
        if(!Schema::hasTable('tables')) {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_name')->nullable();
            $table->integer('seating_capacity')->default(0);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
            $table->enum('status', ['AVAILABLE', 'RESERVED', 'OCCUPIED'])->default('AVAILABLE');
            $table->timestamps();
        });}


        if(!Schema::hasTable('orders')) {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_number')->nullable()->default(null);
            $table->string('customer_name')->nullable();
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('sales_rep_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->enum('order_status', ['PENDING', 'CANCELLED','SERVING', 'SERVED', 'COMPLETED','FOR ALLOCATION'])->default('FOR ALLOCATION')->index('order_status');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('no action')->onUpdate('no action');
            $table->enum('payment_status', ['PAID', 'UNPAID'])->default('UNPAID')->index('payment_status');
            $table->enum('order_type', ['FOOD ORDER', 'PRODUCT ORDER'])->default('FOOD ORDER')->index('order_type');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });}

        if(!Schema::hasTable('order_details')) {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('qty');
            $table->tinyInteger('marked')->default(0);
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });}

        if(!Schema::hasTable('recipes')) {
Schema::create('recipes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade')->onUpdate('cascade');
    $table->foreignId('uom_id')->constrained('unit_of_measures')->onDelete('no action')->onUpdate('no action');
    $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->onUpdate('cascade');
    $table->decimal('qty', 10, 5);
    $table->foreignId('price_level_id')->constrained('price_levels')->onDelete('no action')->onUpdate('no action');
    $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
});}

if(!Schema::hasTable('unit_conversions')) {
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('from_uom_id')->constrained('unit_of_measures')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('to_uom_id')->constrained('unit_of_measures')->onDelete('no action')->onUpdate('no action');
            $table->decimal('conversion_factor', 10, 5);
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
