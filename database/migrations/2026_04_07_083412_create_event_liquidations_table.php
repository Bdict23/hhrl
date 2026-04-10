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
        Schema::create('event_liquidations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('event_id')->nullable()->constrained('banquet_events')->onDelete('cascade');
            $table->enum('status',['DRAFT','OPEN','CLOSED','CANCELLED'])->default('DRAFT')->index();
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('cascade');
            $table->string('purpose')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->foreignId('reviewed_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('reviewed_date')->nullable();
            $table->timestamp('approved_date')->nullable();

            $table->index('reference', 'idx_reference_id');
            $table->index('branch_id', 'idx_branch_id');
            $table->index('event_id', 'idx_event_id');
            $table->index('created_by', 'idx_created_by');
            $table->index('approved_by', 'idx_approved_by');
            $table->index('reviewed_by', 'idx_reviewed_by');
            $table->index('status', 'idx_status');
            $table->index('created_at', 'idx_created_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_liquidations');
    }
};
