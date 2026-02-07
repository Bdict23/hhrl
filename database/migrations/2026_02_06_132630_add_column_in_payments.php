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
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->foreignId('shift_id')->nullable()->default(null)->constrained('cashier_shifts')->onDelete('cascade')->onUpdate('cascade');
            $table->index('shift_id', 'idx_shift');
        });

        Schema::table('invoices', function (Blueprint $table) {
            //
            $table->foreignId('shift_id')->nullable()->default(null)->constrained('cashier_shifts')->onDelete('cascade')->onUpdate('cascade');
            $table->index('shift_id', 'idx_shift');
        });

        Schema::table('order_discounts', function (Blueprint $table) {
            //
            $table->foreignId('shift_id')->nullable()->default(null)->constrained('cashier_shifts')->onDelete('cascade')->onUpdate('cascade');
            $table->index('shift_id', 'idx_shift');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->dropColumn('shift_id');

        });
    }
};
