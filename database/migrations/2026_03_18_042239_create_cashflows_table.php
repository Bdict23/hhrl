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
        Schema::create('cashflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('reference')->unique();
            $table->enum('status', ['DRAFT', 'FINAL', 'CANCELLED'])->default('DRAFT');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->foreignId('approver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->enum('remarks',['BALANCED','SHORT','EXCESS'])->default('BALANCED');
            $table->enum('validation_status', ['UNVALIDATED', 'VALIDATED'])->default('UNVALIDATED');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });
        Schema::table('cashflows', function (Blueprint $table) {
            $table->index('reference', 'idx_reference'); // add index for 'reference'
            $table->index('status', 'idx_status'); // add index for 'status'
            $table->index('approver_id', 'idx_approver_id'); // add index for 'approver_id'
            $table->index('created_by', 'idx_created_by'); // add index for 'created_by'
            $table->index('branch_id', 'idx_branch_id'); // add index for 'branch_id'
            $table->index('validation_status', 'idx_validation_status'); // add index for 'validation_status'
        });

        Schema::create('cashflow_denominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashflow_id')->constrained('cashflows')->onDelete('cascade');
            $table->foreignId('denomination_id')->constrained('denominations')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('amount', 15, 2);
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->index('cashflow_id', 'idx_cashflow_id'); // add index for 'cashflow_id'
            $table->index('denomination_id', 'idx_denomination_id'); // add index for 'denomination_id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_denominations');
        Schema::dropIfExists('cashflows');
    }
};
