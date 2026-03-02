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
            $table->decimal('total_amount', 15, 2)->default(0)->after('customer_id');
        });
        Schema::table('event_venues', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('price_id');
        });
        Schema::table('event_services', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('price_id');
        });
        Schema::table('event_menus', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
        Schema::table('event_venues', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
        Schema::table('event_services', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
        Schema::table('event_menus', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
    }
};
