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
        if (!Schema::hasTable('categories')) {
                 
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name', 255)->nullable();
            $table->string('category_description', 255)->nullable();
            $table->string('status', 255)->default('ACTIVE');    
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        
        }); }

        if (!Schema::hasTable('statuses')) {                    
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 255)->nullable();
            $table->string('status_description', 255)->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        
        });}

        if (!Schema::hasTable('classifications')) {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->string('classification_name', 255)->nullable();
            $table->string('classification_description', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('class_parent')->nullable()->constrained('classifications')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        

        });}

        if (!Schema::hasTable('brands')) {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');    
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        
        });}

        if (!Schema::hasTable('unit_of_measures')) {
        Schema::create('unit_of_measures', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name')->nullable()->comment('ex. kilogram,meter,Liter');
            $table->text('unit_description')->nullable();
            $table->string('unit_symbol', 25)->nullable()->comment('ex. kl,m,L');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');               
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        
        });}

        
        if (!Schema::hasTable('items')) {
            
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->text('item_code')->nullable();
            $table->text('item_description')->nullable();
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->unsignedBigInteger('brand')->nullable();
            $table->unsignedBigInteger('classification_id')->nullable();
            $table->unsignedBigInteger('statuses_id')->nullable();
            $table->unsignedBigInteger('sub_class_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->unsignedBigInteger('updated_by');
            $table->foreignId('company_id')->constrained('companies')->onDelete('no action')->onUpdate('no action');

            
            
             // Foreign key constraints
            $table->foreign('uom_id')->references('id')->on('unit_of_measures');
            $table->foreign('brand')->references('id')->on('brands');
            $table->foreign('classification_id')->references('id')->on('classifications');
            $table->foreign('statuses_id')->references('id')->on('statuses');
            $table->foreign('sub_class_id')->references('id')->on('classifications');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('updated_by')->references('id')->on('employees');            
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        
        });}

if (!Schema::hasTable('menus')) {
    Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->string('menu_image')->nullable()->default(null); // Add the menu_image_name path column
        $table->string('menu_name', 255)->nullable();
        $table->text('menu_description')->nullable();
        $table->string('menu_code', 50)->nullable()->unique();
        $table->foreignId('category_id')->constrained('categories')->onDelete('no action')->onUpdate('no action');
        $table->foreignId('reviewer_id')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
        $table->foreignId('approver_id')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
        $table->datetime('reviewed_date')->nullable()->default(null);
        $table->datetime('approved_date')->nullable()->default(null);
        $table->datetime('rejected_date')->nullable()->default(null);
        $table->enum('status', ['AVAILABLE', 'UNAVAILABLE', 'PENDING', 'INACTIVE', 'REJECTED', 'FOR APPROVAL','FOR REVIEW'])->default('PENDING');        
        $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
        $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
        $table->foreignId('company_id')->constrained('companies')->onDelete('no action')->onUpdate('no action');
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
