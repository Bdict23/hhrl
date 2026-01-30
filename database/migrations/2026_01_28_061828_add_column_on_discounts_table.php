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
        Schema::table('order_discounts', function (Blueprint $table) {
            $table->decimal('calculated_amount', 8, 2)->default(0.00)->after('created_at');
            $table->enum('type', ['ITEM', 'ORDER'])->default('ORDER')->after('calculated_amount');
            $table->enum('status', ['APPLIED', 'CANCELLED'])->default('APPLIED')->after('type');

        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('original_amount', 10, 2)->default(0.00)->after('adjusted_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('order_discounts', function (Blueprint $table) {
            $table->dropColumn('calculated_amount');
            $table->dropColumn('status');
            $table->dropColumn('type');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('original_amount');
        });
    }
};
