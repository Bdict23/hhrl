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
        if(!Schema::hasTable('cheques')) {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('cashier_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->date('cheque_date');
            $table->string('cheque_number');
            $table->string('bank_name');
            $table->foreignId('paid_to_branch')->constrained('branches')->onDelete('no action')->onUpdate('no action');
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
