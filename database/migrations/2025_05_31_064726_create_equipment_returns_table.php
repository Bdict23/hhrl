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
        
        Schema::create('equipment_returns', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique()->comment('Unique reference number for the equipment return');
            $table->string('document_number')->nullable()->comment('Document number associated with the equipment return');
            $table->foreignId('equipment_request_id')->constrained('equipment_requests')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the equipment request this return is associated with');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this return belongs to');
            $table->enum('status', ['DRAFT', 'FINAL'])->default('DRAFT')->comment('Status of the equipment return');
            $table->text('notes')->nullable()->comment('Additional notes or comments regarding the equipment return');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who created the equipment return');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who approved the equipment return');
            $table->foreignId('received_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who received the equipment return');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        }); 

        Schema::table('department_cardex', function (Blueprint $table) {
            $table->enum('status', ['TEMP', 'FINAL'])->default('TEMP')->comment('Status of the department cardex entry');
            $table->unsignedBigInteger('equipment_return_id')->nullable();
            $table->foreign('equipment_return_id')->references('id')->on('equipment_returns')->onDelete('no action')->onUpdate('cascade')->comment('Reference to the equipment return associated with this cardex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_returns');
    }
};
