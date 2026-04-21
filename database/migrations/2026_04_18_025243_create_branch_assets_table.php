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
        Schema::create('batch_properties', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('type_id')->nullable()->constrained('system_parameters')->onDelete('set null');
            $table->enum('status', ['DRAFT','OPEN','CLOSED','CANCELLED'])->default('DRAFT');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('requisition_id')->nullable()->constrained('requisition_infos')->onDelete('set null');
            $table->string('note')->nullable();
            $table->string('purpose')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->timestamp('reviewed_date')->nullable();
            $table->timestamp('issued_date')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('reference', 'idx_reference');
            $table->index('status', 'idx_status');
            $table->index('branch_id', 'idx_branch_id');
            $table->index('requisition_id', 'idx_requisition_id');
            $table->index('approved_by', 'idx_approved_by');
            $table->index('reviewed_by', 'idx_reviewed_by');
            $table->index('approved_date', 'idx_approved_date');
            $table->index('reviewed_date', 'reviewed_date');
            $table->index('created_at', 'idx_created_at');
            $table->index('type_id', 'idx_type_id');


        });

         Schema::create('batch_property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batch_properties')->onDelete('cascade');
            $table->string('code')->unique();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('serial')->nullable()->unique();
            $table->string('sidr_no')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('cost', 15, 2)->default(0.00);
            $table->integer('lifespan')->nullable();
            $table->timestamp('span_ended')->nullable()->comment('automatically calculated based on entered life span');
            $table->enum('condition', ['NEW','USED'])->default('NEW');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();


            $table->index('batch_id', 'idx_batch_id');
            $table->index('code', 'idx_code');
            $table->index('item_id', 'idx_item_id');
            $table->index('branch_id', 'idx_branch_id');
            $table->index('serial', 'idx_serial');
            $table->index('span_ended', 'idx_span_ended');
            $table->index('condition', 'idx_condition');

             });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_property');
        Schema::dropIfExists('batch_property_details');

    }
};
