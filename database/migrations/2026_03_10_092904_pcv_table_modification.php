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
        //
         Schema::create('advance_liquidations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('reference')->nullable()->unique()->index();
            $table->enum('status', ['DRAFT','OPEN', 'CLOSED', 'CANCELLED'])->default('DRAFT');
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('received_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('date_received')->nullable();
            $table->timestamp('date_returned')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->decimal('amount_received', 15, 2)->default(0);
            $table->decimal('amount_returned', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

                // add indexes
                $table->index('reference', 'idx_advance_reference');
                $table->index('status', 'idx_advance_status');
                $table->index('received_by', 'idx_advance_received_by');
                $table->index('approved_by', 'idx_advance_approved_by');
                $table->index('date_received', 'idx_advance_date_received');
                $table->index('company_id', 'idx_advance_company_id');
                $table->index('branch_id', 'idx_advance_branch_id');

         });
        Schema::table('petty_cash_vouchers', function (Blueprint $table) {
            $table->dropForeign(['acknowledgement_receipt_id']);
            $table->dropForeign(['transaction_title_id']);
            $table->foreignId('template_id')->nullable()->constrained('actng_trans_templates')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('banquet_events')->onDelete('cascade');
            $table->dropColumn('acknowledgement_receipt_id');
            $table->dropColumn('transaction_title_id');
            $table->foreignId('advance_liquidation_id')->nullable()->constrained('advance_liquidations')->onDelete('set null');

                // add indexes
                $table->index('template_id', 'idx_template_id');
                $table->index('event_id', 'idx_event_id');
                $table->index('advance_liquidation_id', 'idx_advance_liquidation_id');

    


        });


       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    
    }
};
