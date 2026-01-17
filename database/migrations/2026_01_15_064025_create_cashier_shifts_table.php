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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('branch_id');
             $table->unsignedBigInteger('drawer_id');
            $table->enum('shift_status', ['OPEN', 'CLOSED', 'SUSPENDED'])->default('OPEN');
            $table->timestamp('shift_started')->nullable();
            $table->timestamp('shift_ended')->nullable();
            $table->decimal('starting_cash', 15, 2)->default(0);
            $table->decimal('ending_cash', 15, 2)->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->enum('discrepancy_status', ['NONE', 'SHORT', 'EXCESS'])->default('NONE');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            $table->foreign('drawer_id')->references('id')->on('cash_drawers')->onDelete('cascade');
            $table->foreign('cashier_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
