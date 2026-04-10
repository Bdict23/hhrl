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
        Schema::table('cash_returns', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();

            $table->index('approved_by', 'idx_approved_by');



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
