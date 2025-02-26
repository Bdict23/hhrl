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
        if(!Schema::hasTable('customers')) {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_fname');
            $table->string('customer_lname');
            $table->string('customer_mname')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_relation')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE'])->nullable();
            $table->string('contact_no_1')->nullable();
            $table->string('contact_no_2')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('email')->nullable();
            $table->string('tin')->nullable();
            $table->enum('type', ['INDIVIDUAL', 'WHOLESALE'])->default('INDIVIDUAL');
            $table->foreignId('branch_id')->constrained('branches')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
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
