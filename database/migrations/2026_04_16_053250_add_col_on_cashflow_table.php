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
        Schema::table('cashflows', function (Blueprint $table) {
            $table->decimal('beginning_balance', 15, 2)->default(0.00)->after('amount');
            $table->decimal('ending_balance', 15, 2)->default(0.00)->after('beginning_balance');
            $table->enum('flow_type', ['COLLECTION', 'BANK_DEPOSIT'])->default('COLLECTION')->after('status');
            $table->enum('fund_status', ['PENDING', 'INTRANSIT', 'VALIDATED'])->default('PENDING')->after('flow_type');
            $table->foreignId('parent_id')->nullable()->constrained('cashflows')->onDelete('set null');
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
