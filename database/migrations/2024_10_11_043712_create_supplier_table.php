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
            $table->string('postal_address')->nullable();;
            $table->string('contact_no_1')->nullable();;
            $table->string('supp_address')->nullable();;
            $table->string('contact_no_2')->nullable();
            $table->string('tax_payer_id')->nullable();;
            $table->string('contact_person')->nullable();;
            $table->string('input_tax')->nullable();
            $table->string('supplier_code')->nullable();;
            $table->string('email')->nullable();
            $table->string('supp_status')->default('active');
            $table->timestamps();
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
