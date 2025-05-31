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
        Schema::create('banquet_procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('banquet_events')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the banquet event this procurement belongs to');
            $table->string('reference_number')->nullable()->index('reference_number')->comment('Auto generated Banquet Event Number');
            $table->string('document_number')->nullable()->index('document_number')->comment('Document number for the procurement');
            $table->decimal('suggested_amount', 10, 2)->nullable()->comment('Suggested amount for the procurement');
            $table->decimal('approved_amount', 10, 2)->nullable()->comment('Approved amount for the procurement');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->index('approved_by')->comment('Employee who approved the procurement');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who created the procurement');
            $table->foreignId('noted_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who added notes to the procurement');
            $table->enum('status', ['PREPARING','PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->index('status')->comment('Status of the procurement');
            $table->text('notes')->nullable()->comment('Additional notes or comments regarding the procurement');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this procurement belongs to');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who last updated the procurement');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });

        Schema::table('banquet_procurements', function (Blueprint $table) {
            $table->index(['event_id', 'branch_id'], 'idx_event_branch'); // Index for faster search by event and branch
            $table->index(['reference_number', 'branch_id'], 'idx_reference_branch'); // Index for faster search by reference number and branch
            $table->index(['document_number', 'branch_id'], 'idx_document_branch'); // Index for faster search by document number and branch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banquet_procurements');
    }
};
