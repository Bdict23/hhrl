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
        Schema::table('event_menus', function (Blueprint $table) {
            $table->text('note')->nullable()->after('menu_id');
        });
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('id');
            $table->string('event_address')->nullable()->after('status');
            $table->date('start_date')->nullable()->after('event_address');
            $table->date('end_date')->nullable()->after('start_date');
            $table->time('arrival_time')->nullable()->after('start_date');
            $table->time('departure_time')->nullable()->after('end_date');

            $table->index('reference', 'idx_reference');
             $table->index('start_date', 'idx_start_date');
             $table->index('end_date', 'idx_end_date');
             $table->index('arrival_time', 'idx_arrival_time');
             $table->index('departure_time', 'idx_departure_time');

            // drop columns event date, start_time, end_time from banquet_events table
            $table->dropColumn('event_date');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });

        // delete venue_id column from banquet_events table
            Schema::table('banquet_events', function (Blueprint $table) {
                $table->dropForeign(['venue_id']); // drop foreign key constraint for 'venue_id' if it exists
                $table->dropColumn('venue_id');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('event_menus', function (Blueprint $table) {
            $table->dropColumn('note');
        });
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->dropIndex('idx_reference');
            $table->dropIndex('idx_start_date');
            $table->dropIndex('idx_end_date');
            $table->dropIndex('idx_arrival_time');
            $table->dropIndex('idx_departure_time');
            $table->dropColumn('reference');
            $table->dropColumn('event_address');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('arrival_time');
            $table->dropColumn('departure_time');
        });
    }
};
