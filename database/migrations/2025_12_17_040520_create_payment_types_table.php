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
        if (!Schema::hasTable('payment_types')) {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type_name');
            $table->string('description')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->foreignId('branch_id')->constrained('branches')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_type_id')) {
                    $table->foreignId('payment_type_id')->nullable()->constrained('payment_types')->onUpdate('cascade')->onDelete('set null');
            }
                });
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->change();
        });
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'type')) {
                $table->dropColumn('type');
            }
            $table->enum('type', ['DOWNPAYMENT', 'SERVICE', 'SALES', 'OTHERS'])->default('SALES')->after('invoice_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
