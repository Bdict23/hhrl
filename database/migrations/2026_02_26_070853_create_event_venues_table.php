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
        Schema::create('event_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('banquet_events')->onDelete('cascade');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->foreignId('price_id')->constrained('price_levels')->onDelete('cascade');
            $table->integer('qty')->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

             $table->index('event_id', 'idx_event');
             $table->index('venue_id', 'idx_venue');
             $table->index('price_id', 'idx_price');
             $table->index('start_date', 'idx_start_date');
             $table->index('end_date', 'idx_end_date');
             $table->index(['event_id', 'venue_id'], 'idx_event_venue');
             $table->index(['event_id', 'price_id'], 'idx_event_price');
             $table->index(['venue_id', 'price_id'], 'idx_venue_price');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_venues');
    }
};
