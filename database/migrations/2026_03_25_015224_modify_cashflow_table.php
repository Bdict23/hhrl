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
           //drop validation status column
           $table->dropColumn('validation_status');
           // drop status column and add new status column with enum values PENDING, APPROVED, REJECTED
            $table->dropColumn('status');
           $table->enum('status', ['DRAFT', 'OPEN', 'CLOSED', 'CANCELLED'])->default('DRAFT')->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashflows', function (Blueprint $table) {
            //add validation status column
            $table->string('validation_status')->nullable()->after('id');
        });
    }
};
