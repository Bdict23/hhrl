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
        Schema::table('order_details',function(Blueprint $table){
            $table->foreignId('price_level_cost')->nullable()->constrained('price_levels')->onDelete('no action')->onUpdate('no action');
        });

        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('CANCELLED','CLOSED','OPEN','PARTIAL_REFUND') NOT NULL DEFAULT 'OPEN'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('order_details',function(Blueprint $table){
            $table->dropForeign(['price_level_cost']);
            $table->dropColumn('price_level_cost');
        });
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('CANCELLED','CLOSED','OPEN') NOT NULL DEFAULT 'OPEN'");
    }
};
