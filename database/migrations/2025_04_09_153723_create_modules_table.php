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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->unique();
            $table->string('module_description')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });
        DB::table('modules')->insert([
            ['module_name' => 'Purchase order', 'module_description' => 'Description for Module 1'],
            ['module_name' => 'Purchase Receiving', 'module_description' => 'Description for Module 2'],
            ['module_name' => 'Item', 'module_description' => 'Description for Module 3'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
