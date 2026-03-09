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
        Schema::create('actng_account_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('type_name');
            $table->string('acct_code');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->boolean('is_active')->default(true)->index()->comment('Indicates whether active or not');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
             $table->index('acct_code', 'idx_acct_code'); // add index for 'acct_code'
             $table->index('type_name', 'idx_type_name'); // add index for 'type_name'
             $table->index('is_active', 'idx_is_active'); // add index for 'is_active'
             $table->index('company_id', 'idx_account_types_company_id'); // add index for 'company_id'
        });
        Schema::create('actng_chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('transaction_type')->constrained('actng_account_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('actng_chart_of_accounts')->onDelete('cascade');
            $table->string('account_code')->nullable();
            $table->string('account_label')->nullable();
            $table->enum('normal_balance', ['DEBIT','CREDIT'])->default('DEBIT');
            $table->string('account_title');
            $table->boolean('is_active')->default(true)->comment('Indicates whether active or not')->index();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            $table->index('account_code', 'idx_account_code'); // add index for 'account_code'
            $table->index('account_title', 'idx_account_title'); // add index for 'account_title'
            $table->index('is_active', 'idx_coa_is_active'); // add index for 'is_active'
            $table->index('normal_balance', 'idx_normal_balance'); // add index for 'normal_balance'
            $table->index('transaction_type', 'idx_transaction_type'); // add index for 'transaction_type'
            $table->index('company_id', 'idx_account_types_company_id'); // add index for 'company_id'
            $table->index('parent_id', 'idx_parent'); // add index for 'parent'
            $table->index('account_label', 'idx_account_label'); // add index for 'account_label'
            
        });

        // 3. TRANSACTION TEMPLATES (The Rules Header)
        Schema::create('actng_trans_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('template_name'); // e.g., "Internet Payment via PCV"
            $table->string('description')->nullable();
            $table->foreignId('transaction_type')->constrained('actng_account_types')->onDelete('cascade');
            $table->string('module_type')->default('PCV'); // e.g., "PCV", "AP", "AR" - for categorization/filtering
            $table->boolean('is_active')->default(true)->comment('Indicates whether active or not')->index();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->index('company_id', 'idx_account_types_company_id'); // add index for 'company_id'
                $table->index('module_type', 'idx_module_type'); // add index for 'module_type'
                $table->index('transaction_type', 'idx_transaction_type'); // add index for 'transaction_type'
                $table->index('is_active', 'idx_template_is_active'); // add index for 'is_active'
        });

        // 4. TEMPLATE DETAILS (The DR/CR Mapping)
        Schema::create('actng_trans_template_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('actng_trans_templates')->onDelete('cascade');
            $table->foreignId('account_title_id')->constrained('actng_chart_of_accounts')->onDelete('cascade');
            $table->enum('type', ['DEBIT', 'CREDIT']);
        });
        

         Schema::table('petty_cash_vouchers', function (Blueprint $table) {
            $table->foreignId('account_types_id')->nullable()->constrained('actng_account_types')->onDelete('set null')->comment('accounting transaction_type table');
            $table->string('account_type')->nullable()->comment('accounting transaction_type table storing by string');
            $table->foreignId('transaction_title_id')->nullable()->constrained('actng_chart_of_accounts')->onDelete('set null');
            $table->string('transaction_title')->nullable()->comment('accounting transaction_title table storing by string');
        });

        Schema::table('petty_cash_voucher_details', function (Blueprint $table) {
            $table->foreignId('transaction_title_id')->nullable()->constrained('actng_chart_of_accounts', 'id', 'fk_pcv_details_trn_detail')->onDelete('set null');
            $table->enum('type', ['DEBIT','CREDIT'])->default('CREDIT');
        });
         


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop indexes and columns first before dropping tables
         Schema::table('petty_cash_voucher_details', function (Blueprint $table) {
            $table->dropForeign(['transaction_title_id']);
            $table->dropColumn(['transaction_title_id', 'type']);
        });

        Schema::dropIfExists('actng_account_types');
        Schema::dropIfExists('actng_chart_of_accounts');
        Schema::dropIfExists('actng_coa_trans_templates');
      


    }
};
