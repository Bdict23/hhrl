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
        Schema::create('event_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('banquet_events')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the banquet event this menu belongs to');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the menu item');
            $table->integer('qty')->default(1)->comment('Quantity of the menu item for the event');
            $table->foreignId('price_id')->constrained('price_levels')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the price level for this menu item');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change;
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_menus');
    }
};
