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

        IF (!Schema::hasTable('CONSUMPTIONS')) {
            Schema::create('CONSUMPTIONS', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->string('consumption_number')->nullable()->index();
                $table->foreignId('prepared_by')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->foreignId('checked_by')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->foreignId('approved_by')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->text('remarks_id')->nullable();
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            });
        }

        if (!Schema::hasTable('cardex')) {
            Schema::create('cardex', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->string('qty_in')->nullable()->default('0');
                $table->string('qty_out')->nullable()->default('0');
                $table->date('expiration_date')->nullable();
                $table->date('manufactured_date')->nullable();
                $table->foreignId('item_id')->constrained('items')->onDelete('no action')->onUpdate('no action');
                $table->enum('status', ['TEMP', 'RESERVED', 'FINAL', 'CANCELLED'])->default('TEMP');
                $table->enum('transaction_type', ['STF', 'RECEVING', 'ADJUSTMENT', 'SALES', 'SALES-RETURN','CONSUMPTION']);
                $table->foreignId('price_level_id')->nullable()->default(null)->constrained('price_levels')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('invoice_id')->nullable()->default(null)->constrained('invoices')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('cunsumption_id')->nullable()->default(null)->constrained('consumptions')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('stf_id')->nullable()->default(null)->constrained('stocktransfer_infos')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('receiving_id')->nullable()->default(null)->constrained('receivings')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('requisition_id')->nullable()->default(null)->constrained('requisition_infos')->onDelete('no action')->onUpdate('no action');
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
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
