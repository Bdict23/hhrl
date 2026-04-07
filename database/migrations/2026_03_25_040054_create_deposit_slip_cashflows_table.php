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
        Schema::create('deposit_slip_cashflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('deposit_slip_validation_id')->constrained('deposit_slip_validations', 'id', 'ds_cashflows_val_id_foreign')->onDelete('cascade');
            $table->foreignId('cashflow_id')->constrained('cashflows')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            // indexes
            $table->index('deposit_slip_validation_id', 'idx_deposit_slip_validation_id');
            $table->index('cashflow_id', 'idx_cashflow_id');
            $table->index('branch_id', 'idx_branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_slip_cashflows');
    }
};
