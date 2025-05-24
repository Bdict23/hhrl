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
        if(!Schema::hasTable('payments')) {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('prepared_by')->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->enum('status', ['PARTIAL', 'FULL'])->default('FULL')->index('status')->comment('Payment status, partial or full');
            $table->enum('payment_method', ['CASH', 'CHEQUE', 'BANK TRANSFER', 'ONLINE'])->default('CASH')->index('payment_method')->comment('Payment method, cash, cheque, or bank transfer');
            $table->enum('type', ['DOWNPAYMENT', 'SERVICE', 'ITEM', 'OTHERS'])->default('ITEM')->index('type')->comment('Type of payment, downpayment, service, item, or others');
            $table->foreignId('cheque_id')->nullable()->constrained('cheques')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('no action')->onUpdate('no action');
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
