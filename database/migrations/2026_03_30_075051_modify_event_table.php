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
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->enum('liquidation_status', ['PENDING', 'LIQUIDATED','CANCELLED'])->default('PENDING')->after('status');
            $table->date('liquidation_date')->nullable()->after('liquidation_status');
        });

        DB::statement("ALTER TABLE banquet_events MODIFY COLUMN status 
            ENUM('PENDING','CONFIRMED','CLOSED','UNATTENDED','CANCELLED') 
            NOT NULL DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->dropColumn('liquidation_status');
            $table->dropColumn('liquidation_date');
        });
    }
};
