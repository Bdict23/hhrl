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
            if (!Schema::hasTable('signatories')) {
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->ENUM('status', ['ACTIVE', 'INACTIVE'])->notNullable()->default('active');
            $table->string('signatory_type');
            $table->string('module')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
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
