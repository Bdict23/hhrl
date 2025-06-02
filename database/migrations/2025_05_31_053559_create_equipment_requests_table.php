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
        Schema::create('equipment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique()->nullable()->comment('Unique reference number for the equipment request');
            $table->string('document_number')->nullable()->comment('Document number for the equipment request');
            $table->foreignId('event_id')->nullable()->constrained('banquet_events')->onDelete('set null')->onUpdate('cascade')->comment('Reference to the related banquet procurement');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the department making the request');
            $table->date('event_date')->nullable()->comment('Date of the event for which the equipment is requested');
            $table->timestamp('from_time')->nullable()->comment('Start time when the equipment is intended to be used');
            $table->timestamp('to_time')->nullable()->comment('End time when the equipment is intended to be used');
            $table->foreignId('requested_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who requested the equipment');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who approved the request');
            $table->foreignId('received_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who received the equipment');
            $table->enum('status', ['PREPARING','PENDING','RELEASED','REJECTED','RETURNED','FLAGGED'])->default('PREPARING')->index('status')->comment('Status of the equipment request');
            $table->text('notes')->nullable()->comment('Additional notes or comments regarding the equipment request');
            $table->string('layout')->nullable()->comment('Image file name or path for the layout');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this request belongs to');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            
            $table->index(['reference_number', 'branch_id'], 'idx_reference_branch'); // Index for faster search by reference number and branchID
            $table->index(['approved_by', 'branch_id'], 'idx_approved_branch'); // Index for faster search by approved_by and branchID
            $table->index(['received_by', 'branch_id'], 'idx_received_branch'); // Index for faster search by received_by and branchID
            $table->index(['approved_by', 'status', 'branch_id'], 'idx_approved_status_branch'); // Index for faster search by approved_by, status, and 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_requests');
    }
};
