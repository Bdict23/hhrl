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
        if (!Schema::hasTable('terms')) {

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string('term_name', 55)->nullable();
            $table->string('term_description', 255)->nullable();
            $table->integer('payment_days')->nullable();
            $table->enum('term_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });

        // Insert data into the table
    DB::table('terms')->insert([
    
        [
            'term_name' => 'Net 30',
            'term_description' => 'Payment due in 30 days',
            'payment_days' => 30,
            'term_status' => 'ACTIVE',
            'company_id' => null,
            'created_by' => null,
        ],
        [
            'term_name' => 'Net 60',
            'term_description' => 'Payment due in 60 days',
            'payment_days' => 60,
            'term_status' => 'ACTIVE',
            'company_id' => null,
            'created_by' => null,
        ],
        [
            'term_name' => 'Net 90',
            'term_description' => 'Payment due in 90 days',
            'payment_days' => 90,
            'term_status' => 'ACTIVE',
            'company_id' => null,
            'created_by' => null,
        ],
    

    ]);

        }



        // Create requisition_infos table
        if (!Schema::hasTable('requisition_infos')) {
            Schema::create('requisition_infos', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->string('requisition_number', 155); // Set as Primary Key
                $table->unsignedBigInteger('from_branch_id'); // Foreign key to branches table
                $table->unsignedBigInteger('to_branch_id')->nullable(); // Nullable foreign key
                $table->date('trans_date')->nullable(); // Transaction date
                $table->string('merchandise_po_number', 55)->nullable(); // Merchandise PO number
                $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('reviewed_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
                $table->enum('category', ['PO', 'PR']); // Category
                $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('no action')->onUpdate('no action');
                $table->enum('requisition_status', ['TO RECEIVE','PARTIALLY FULLFILLED', 'FOR APPROVAL', 'FOR REVIEW','REJECTED', 'PREPARING', 'COMPLETED', 'CANCELLED'])->default('PREPARING'); // Status
                $table->text('remarks')->nullable(); // Remarks
                $table->date('approved_date')->nullable(); // Approved date
                $table->date('rejected_date')->nullable(); // Rejected date
                $table->date('reviewed_date')->nullable(); // Cancelled date
                $table->unsignedBigInteger('supplier_id')->nullable(); // Foreign key to suppliers table

                // Foreign key constraints
                $table->foreign('from_branch_id')->references('id')->on('branches');
                $table->foreign('to_branch_id')->references('id')->on('branches');
                $table->foreign('supplier_id')->references('id')->on('suppliers');

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
