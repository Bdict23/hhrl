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
        Schema::table('banquet_procurements', function (Blueprint $table) {
            $table->boolean('services_included')->default(false);
             $table->dropColumn('approved_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banquet_procurements', function (Blueprint $table) {
            $table->dropColumn('services_included');
        });
    }
};
