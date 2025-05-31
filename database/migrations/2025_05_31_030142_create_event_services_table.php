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
        Schema::create('event_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('banquet_events')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the banquet event this service belongs to');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the service provided for the event');
            $table->foreignId('price_id')->constrained('price_levels')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the price level for this service');
            $table->integer('qty')->nullable()->comment('Quantity of the service for the event');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change;

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_services');
    }
};
