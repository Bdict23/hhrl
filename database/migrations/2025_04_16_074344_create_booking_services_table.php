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
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_records_id');
            $table->unsignedBigInteger('leisure_id');
            $table->unsignedBigInteger('booking_payment_id');
            $table->decimal('amount')->default(0);
            $table->integer('quantity')->default(0);
            $table->decimal('total_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};
