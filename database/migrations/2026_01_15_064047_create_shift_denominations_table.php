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
         Schema::create('denominations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['BILL', 'COIN']);
            $table->decimal('value', 15, 2);
            $table->timestamps();
        });
        Schema::create('shift_denominations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->enum('counter_type', ['STARTING_CASH', 'ENDING_CASH']);
            $table->decimal('amount', 15, 2);
            $table->integer('quantity')->default(0);
            $table->unsignedBigInteger('denomination_id');

            $table->foreign('shift_id')->references('id')->on('cashier_shifts')->onDelete('cascade');
            $table->foreign('denomination_id')->references('id')->on('denominations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('denominations');
        Schema::dropIfExists('shift_denominations');
    }
};
