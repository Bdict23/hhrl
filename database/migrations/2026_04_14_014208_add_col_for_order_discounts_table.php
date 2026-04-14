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
            $table->unsignedBigInteger('branch_id')->after('discount_id')->nullable();

            // Add foreign key constraint
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('order_discounts', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['branch_id']);

            // Drop the column
            $table->dropColumn('branch_id');
        });

    }
};
