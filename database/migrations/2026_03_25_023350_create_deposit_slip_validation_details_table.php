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
        
        Schema::create('deposit_slip_validation_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('deposit_slip_validation_id')->constrained('deposit_slip_validations', 'id', 'ds_val_checks_val_id_foreign')->onDelete('cascade');
            $table->foreignId('check_id')->nullable()->constrained('cheques')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

                // indexes
                $table->index('deposit_slip_validation_id', 'idx_deposit_slip_validation_id');
                $table->index('check_id', 'idx_check_id');
                $table->index('branch_id', 'idx_branch_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_slip_validation_checks');
    }
};
