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
    

        Schema::create('acknowledgement_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('banquet_events')->onDelete('cascade');
            $table->string('reference')->nullable()->unique()->index();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->enum('status', ['DRAFT','OPEN', 'CLOSED', 'CANCELLED'])->default('DRAFT');
            $table->text('notes')->nullable();
            //check details
            $table->string('check_number')->nullable();
            $table->decimal('check_amount', 15, 2)->default(0);
            $table->date('check_date')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->string('account_name')->nullable();
            $table->string('amount_in_words')->nullable();
            $table->enum('check_status', ['CURRENT', 'POST-DATED'])->default('CURRENT');
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            // add indexes
            $table->index('reference', 'idx_reference');
            $table->index('customer_id', 'idx_customer_id');
            $table->index('status', 'idx_status');
            $table->index('check_number', 'idx_check_number');
            $table->index('check_status', 'idx_check_status');
            $table->index('bank_id', 'idx_bank_id');
            $table->index('created_by', 'idx_created_by');

        });

        Schema::table('banks', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acknowledgement_receipts');
        Schema::table('banks', function (Blueprint $table) {
            $table->dropIndex('idx_bank_name');
        });
    }
};
