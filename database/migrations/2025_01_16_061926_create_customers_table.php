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
            $table->string('customer_fname')->nullable()->index('customer_fname_index');
            $table->string('customer_lname')->nullable()->index('customer_lname_index');
            $table->string('customer_mname')->nullable()->index('customer_mname_index');
            $table->string('contact_person')->nullable();
            $table->string('contact_person_relation')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE'])->nullable()->index('gender_index');
            $table->string('contact_no_1')->nullable();
            $table->string('contact_no_2')->nullable();
            $table->string('customer_address')->nullable()->index('customer_address_index');
            $table->string('email')->nullable();
            $table->string('tin')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('type', ['INDIVIDUAL', 'WHOLESALE'])->default('INDIVIDUAL')->index('type_index');
            $table->foreignId('branch_id')->constrained('branches')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent()->index('created_at_index'); // Set default value to current timestamp
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
