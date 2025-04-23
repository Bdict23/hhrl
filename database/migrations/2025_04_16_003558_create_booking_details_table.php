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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_records_id');
            $table->string('customer_category');
            $table->integer('male_count')->default(0);
            $table->integer('female_count')->default(0);
            $table->decimal('entrance_fee')->default(0);
            $table->integer('total_count')->default(0);
            $table->decimal('total_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
