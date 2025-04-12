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
            // STF TYPES TABLE
            if (!Schema::hasTable('stf_types')) {
                Schema::create('stf_types', function (Blueprint $table) {
                    $table->id();
                    $table->string('type_name');
                    $table->string('type_description')->nullable();
                    $table->string('type_status')->default('active');
                    $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
                    $table->timestamps();
                });


                // Insert data into the table
                DB::table('stf_types')->insert([
                    ['type_name' => 'REQUEST', 'type_description' => 'Requisition items'],
                    ['type_name' => 'SALES DRIVE', 'type_description' => 'Sales drive items'],
                ]);
            }


            // STOCK TRANSFER TABLE
        if (!Schema::hasTable('stocktransfer_infos')){
            Schema::create('stocktransfer_infos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stf_type_id')->constrained('stf_types')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('from_branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('to_branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('prepared_by')->constrained('employees')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('driver')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->foreignId('reviewed_by')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->foreignId('picked_by')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->foreignId('packed_by')->constrained('employees')->onDelete('no action')->onUpdate('no action')->nullable();
                $table->string('stock_transfer_number', 100)->unique();
                $table->date('transaction_date');
                $table->date('dispatched_date')->nullable();
                $table->text('remarks')->nullable();
                $table->enum('STF_STATUS', ['PREPARED','NEW', 'VIEWED', 'PICKING', 'PACKING','REVIEW','APPROVAL', 'INTRANSIT', 'RECEIVED', 'CANCELLED'])->notNullable()->default('PREPARED');
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

                $table->index('transaction_date');


            });
        }

        // RECEIVING TABLE
        if (!Schema::hasTable('receivings')) {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('REQUISITION_ID')->nullable()->constrained('requisition_infos')->onDelete('no action')->onUpdate('no action');
            $table->enum('RECEIVING_TYPE', ['STF', 'PO'])->nullable();
            $table->string('RECEIVING_NUMBER', 100)->nullable();
            $table->string('WAYBILL_NUMBER', 30)->nullable();
            $table->string('DELIVERY_NUMBER', 30)->nullable();
            $table->string('INVOICE_NUMBER', 30)->nullable();
            $table->enum('RECEIVING_STATUS', ['FINAL', 'DRAFT'])->default('DRAFT');
            $table->text('remarks')->nullable();
            $table->enum('RECEIVING_STATUS', ['FINAL', 'CANCELLED', 'TEMP'])->default('TEMP');
            $table->foreignId('PREPARED_BY')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->string('DELIVERED_BY', 100)->nullable();
            $table->foreignId('stf_id')->nullable()->default(null)->constrained('stocktransfer_infos')->onDelete('no action')->onUpdate('no action');
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

    }
};
