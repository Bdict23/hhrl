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
        //
        Schema::create('system_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('key')->comment('The key for the system parameter, e.g., "requisition_type" , payment_type"')->index();
            $table->string('name')->comment('The name for the system parameter, e.g., "AFL" for requisition_type or "CASH", "CHECK" for payment_type');
            $table->string('description')->nullable()->comment('A description of the system parameter');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Add indexes for faster queries
            $table->index('status', 'idx_status');
            $table->index('key', 'idx_key');
            $table->index('branch_id', 'idx_branch_id');
            $table->index('module_id', 'idx_module_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
