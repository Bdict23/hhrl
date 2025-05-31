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
        Schema::create('department_descripancy_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the department this record belongs to');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this record belongs to');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the item in the discrepancy record');
            $table->foreignId('equipment_request_id')->constrained('equipment_requests')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the equipment request related to this record');
            $table->enum('status', ['ACTIVE', 'SETTELED', 'CANCELLED'])->default('ACTIVE')->comment('Status of the discrepancy record');
            $table->text('remarks')->nullable()->comment('Additional remarks or notes for the discrepancy record');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_descripancy_records');
    }
};
