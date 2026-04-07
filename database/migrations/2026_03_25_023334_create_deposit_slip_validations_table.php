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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');   
            $table->foreignId('bank_id')->constrained('banks')->onDelete('cascade');
            $table->string('account_number')->unique();
            $table->string('account_name');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            // indexes
            $table->index('account_number', 'idx_account_number');
            $table->index('bank_id', 'idx_bank_id');
            $table->index('branch_id', 'idx_branch_id');
            $table->index('company_id', 'idx_company_id');
            
        });
    
        Schema::create('deposit_slip_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->decimal('cash_amount', 15, 2)->default(0);
            $table->decimal('check_amount', 15, 2)->default(0);
            $table->boolean('is_picked_up')->default(false);
            $table->date('cashflow_start_date');
            $table->date('cashflow_end_date');
            $table->date('deposit_date')->nullable();
            $table->foreignId('deposited_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('validation_status', ['UNVALIDATED', 'CANCELLED', 'VALIDATED'])->default('UNVALIDATED');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            // indexes
            $table->index('reference_number', 'idx_reference_number');
            $table->index('bank_account_id', 'idx_bank_account_id');
            $table->index('branch_id', 'idx_branch_id');
            $table->index('validation_status', 'idx_validation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_slip_validations');
        Schema::dropIfExists('bank_accounts');
    }
};
