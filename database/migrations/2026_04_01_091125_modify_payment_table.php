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
        DB::statement("ALTER TABLE payments MODIFY COLUMN type 
            ENUM('DOWNPAYMENT','SERVICE','SALES','REFUND','VOID', 'BEO', 'ENTRANCE', 'RENTAL', 'CORKAGE', 'OTHER') 
            NOT NULL DEFAULT 'SALES'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
