<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        IF (!Schema::hasTable('withdrawals')) {
            Schema::create('withdrawals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->string('reference_number')->nullable()->index();
                $table->timestamp('usage_date')->useCurrent()->index('usage_date');
                $table->date('useful_date')->nullable()->index('useful_date');
                $table->foreignId('prepared_by')->constrained('employees')->onDelete('no action')->onUpdate('cascade')->nullable();
                $table->foreignId('reviewed_by')->constrained('employees')->onDelete('no action')->onUpdate('cascade')->nullable();
                $table->foreignId('approved_by')->constrained('employees')->onDelete('no action')->onUpdate('cascade')->nullable();
                $table->foreignId('department_id')->constrained('departments')->onDelete('no action')->onUpdate('cascade');
                $table->text('remarks')->nullable();
                $table->enum('withdrawal_status', ['FOR APPROVAL', 'REJECTED', 'FOR REVIEW','PREPARING','CANCELLED','APPROVED'])->default('PREPARING')->index('withdrawal_status');
                $table->date('approved_date')->nullable();
                $table->date('reviewed_date')->nullable();
                $table->date('rejected_date')->nullable();
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

                
            });
        }

        if (!Schema::hasTable('cardex')) {
            Schema::create('cardex', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->decimal('qty_in', 10, 2)->nullable()->default(0);
                $table->decimal('qty_out', 10, 2)->nullable()->default(0);
                $table->date('expiration_date')->nullable()->index('expiration_date');
                $table->date('manufactured_date')->nullable();
                $table->foreignId('item_id')->constrained('items')->onDelete('no action')->onUpdate('no action');
                $table->enum('status', ['TEMP', 'RESERVED', 'FINAL', 'CANCELLED'])->default('TEMP');
                $table->enum('transaction_type', ['STF', 'RECEVING', 'ADJUSTMENT', 'SALES', 'SALES-RETURN','WITHDRAWAL'])->index('transaction_type');
                $table->foreignId('price_level_id')->nullable()->default(null)->constrained('price_levels')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('invoice_id')->nullable()->default(null)->constrained('invoices')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('withdrawal_id')->nullable()->default(null)->constrained('withdrawals')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('stf_id')->nullable()->default(null)->constrained('stocktransfer_infos')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('receiving_id')->nullable()->default(null)->constrained('receivings')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('requisition_id')->nullable()->default(null)->constrained('requisition_infos')->onDelete('no action')->onUpdate('no action');
                $table->timestamp('final_date')->nullable();
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

                $table->index('expiration_date');
                $table->index('manufactured_date');
                $table->index('status');
                $table->index('transaction_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
