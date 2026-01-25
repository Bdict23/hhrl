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
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('order_round')->default(1)->after('invoice_type')->comment('to track multiple rounds of orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('order_round');
        });
    }
};
