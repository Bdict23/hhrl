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
        if(!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->comment('Invoice number');
            $table->enum('invoice_type', ['SALES', 'SERVICE', 'CHARGE', 'COLLECTION RECEIPT', 'OFFICIAL RECEIPT'])->default('SALES')->index('invoice_type')->comment('Invoice type, sales, service, charge, collection receipt, official receipt');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->enum('status', ['CANCELLED', 'CLOSED', 'OPEN'])->default('OPEN')->index('status')->comment('Status of the invoice');
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->decimal('amount', 10, 2)->default(0.00)->comment('Invoice amount');
            $table->enum('payment_mode', ['CREDIT', 'CASH'])->default('CASH')->index('payment_mode')->comment('Payment mode, credit or cash');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('no action')->onUpdate('no action');
            $table->string('customer_name')->nullable()->comment('Walk in customer name');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
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
