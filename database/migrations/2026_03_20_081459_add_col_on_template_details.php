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
        // ADD CREATED AT AND UPDATED AT TO DETAILS
        Schema::table('actng_trans_template_details', function (Blueprint $table) {
            if (!Schema::hasColumn('actng_trans_template_details', 'created_at') && !Schema::hasColumn('actng_trans_template_details', 'updated_at')) {
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            }
            });

        Schema::table('actng_chart_of_accounts', function (Blueprint $table) {
                $table->dropForeign(['transaction_type']);
            
        });

        Schema::table('actng_account_types', function (Blueprint $table) {
                $table->dropColumn('acct_code');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actng_trans_template_details', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('actng_chart_of_accounts', function (Blueprint $table) {
            $table->string('transaction_type')->nullable();
        });

        Schema::table('actng_account_types', function (Blueprint $table) {
            $table->string('acct_code')->nullable();
        });
    }
};
