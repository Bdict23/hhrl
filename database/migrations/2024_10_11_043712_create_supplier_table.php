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
        if (!Schema::hasTable('suppliers')) {


        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supp_name');
            $table->string('postal_address')->nullable();
            $table->string('contact_no_1')->nullable();
            $table->string('supp_address')->nullable();
            $table->string('contact_no_2')->nullable();
            $table->string('tin_number')->nullable();;
            $table->string('contact_person')->nullable();
            $table->enum('input_tax', ['NON-VAT', 'VAT', 'UNDECLARED'])->default('UNDECLARED')->index('input_tax_index')->comment('Type of input tax for the supplier');
            $table->string('supplier_code')->nullable()->index('supplier_code_index')->comment('Unique code for the supplier');
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->enum('supplier_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE')->index('supplier_status_index')->comment('Status of the supplier, active or inactive');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
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
