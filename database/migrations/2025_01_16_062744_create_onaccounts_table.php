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
        if(!Schema::hasTable('onaccounts')) {
        Schema::create('onaccounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['OPEN', 'CLOSED', 'CANCELLED'])->default('OPEN')->index('status')->comment('Status of the onaccount');
            $table->enum('payment_scheme', ['DAILY', 'WEEKLY', 'BI-MONTHLY', 'MONTHLY'])->nullable()->index('payment_scheme')->comment('Payment scheme for the onaccount');
            $table->integer('terms')->default(0)->index('terms')->comment('Number of terms for the onaccount');
            $table->decimal('amount_due', 10, 2)->default(0.00)->comment('Amount due for the onaccount');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
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
