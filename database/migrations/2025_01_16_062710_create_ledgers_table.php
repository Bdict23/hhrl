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
        if(!Schema::hasTable('ledgers')) {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('debit', 10, 2)->default(0.00);
            $table->decimal('credit', 10, 2)->default(0.00);
            $table->enum('type', ['PAYMENT', 'DMCM'])->index('type')->default('PAYMENT')->comment('Ledger type, payment or dmcm');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
