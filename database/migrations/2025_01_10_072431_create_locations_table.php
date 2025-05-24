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
        if(!Schema::hasTable('locations')) {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_name')->nullable()->default(null)->index('location_name_index');
            $table->string('location_group')->nullable()->default(null)->index('location_group_index');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
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
