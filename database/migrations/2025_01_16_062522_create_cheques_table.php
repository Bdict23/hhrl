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
        //create a bank table if it does not exist
        if(!Schema::hasTable('banks')) {
            Schema::create('banks', function (Blueprint $table) {
                $table->id();
                $table->string('bank_name')->nullable()->index('bank_name');
                $table->string('bank_code')->nullable()->index('bank_code');
                $table->string('bank_address')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('email')->nullable();
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            });
        }

        if(!Schema::hasTable('cheques')) {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('cashier_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->date('cheque_date')->useCurrent()->index('cheque_date');
            $table->string('cheque_number')->index('cheque_number');
            $table->enum('cheque_status', ['PENDING', 'CLEARED', 'RETURNED', 'CANCELLED'])->default('PENDING')->index('cheque_status');
            $table->foreignId('bank_id')->constrained('banks')->onDelete('no action')->onUpdate('no action');
            $table->text('remarks')->nullable();
            $table->date('cleared_date')->nullable()->index('cleared_date');
            $table->date('returned_date')->nullable()->index('returned_date');
            $table->date('cancelled_date')->nullable()->index('cancelled_date');
            $table->foreignId('paid_to_branch')->constrained('branches')->onDelete('no action')->onUpdate('no action');
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
