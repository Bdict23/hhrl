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
        
        Schema::create('petty_cash_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('acknowledgement_receipt_id')->nullable()->constrained('acknowledgement_receipts')->onDelete('set null');
            $table->string('reference')->nullable()->unique()->index()->comment('Unique reference number for the petty cash voucher auto-generated');
            $table->string('voucher_number')->nullable()->unique()->index()->comment('Unique voucher number for the petty cash voucher auto-generated');
            $table->foreignId('paid_to_employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('paid_to_customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('purpose')->nullable();
            $table->enum('status', ['DRAFT', 'OPEN', 'CLOSED', 'CANCELLED'])->default('DRAFT');
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });

        Schema::create('petty_cash_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_cash_voucher_id')->constrained('petty_cash_vouchers')->onDelete('cascade');
            $table->string('transaction_title')->nullable()->comment('Description of the transaction detail stored by string');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_voucher_details');
        Schema::dropIfExists('petty_cash_vouchers');
    }
};
