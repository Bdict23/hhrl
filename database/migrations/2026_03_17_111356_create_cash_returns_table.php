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
        Schema::create('cash_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('reference')->nullable()->unique()->index();
            $table->enum('status', ['DRAFT','FINAL', 'CANCELLED'])->default('DRAFT');
            $table->foreignId('pcv_id')->nullable()->constrained('petty_cash_vouchers')->onDelete('set null');
            $table->foreignId('event_id')->nullable()->constrained('banquet_events')->onDelete('set null');
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->decimal('amount_returned', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_returns');
    }
};
