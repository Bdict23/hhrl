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
            $table->string('reference')->nullable()->after('invoice_type')->comment('to store unique reference for each invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
         Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('reference');
         }
         );
}    
};
