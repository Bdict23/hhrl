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
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('price_level_id')->nullable()->constrained('price_levels')->onUpdate('cascade')->comment('Reference to the price level applied to this order detail');
        });

        Schema::create('order_discounts', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('discount_id')->constrained('discounts')->onDelete('cascade');
                    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                    $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->onDelete('cascade');
                    $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['price_level_id']);
            $table->dropColumn('price_level_id');
        });
        Schema::dropIfExists('order_discounts');
    }
};
