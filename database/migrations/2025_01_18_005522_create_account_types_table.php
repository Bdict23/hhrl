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
        if(!Schema::hasTable('account_types')) {
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('account_type_name', 255)->nullable();
            $table->string('account_type_description', 255)->nullable();
            $table->foreignId('created_by')->constrained('employees')->nullable();

            $table->timestamps();
        });}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
