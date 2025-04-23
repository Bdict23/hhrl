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


        // Schema::create('leisures', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->float('amount');
        //     $table->string('description');
        //     $table->integer('status');
        //     $table->unsignedBigInteger('branch_id');
        //     $table->timestamps();
        // });

        // Schema::create('booking_records', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('booking_number');
        //     $table->unsignedBigInteger('customers_id');
        //     $table->unsignedBigInteger('branch_id');
        //     $table->foreign('customers_id')->references('id')->on('customers')->onDelete('cascade');
        //     $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        //     $table->string('booking_status');
        //     $table->timestamps();
        // });

        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->string('OR_number');
            $table->string('payment_type');
            $table->decimal('amount_due');
            $table->decimal('amount_payed');
            $table->decimal('balance');
            $table->string('payment_status');
            $table->string('booking_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //

        Schema::dropIfExists('leisures');
        Schema::dropIfExists('booking_records');
        Schema::dropIfExists('booking_payments');
    }
};
