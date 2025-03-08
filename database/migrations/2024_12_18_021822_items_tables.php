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

        if(!Schema::hasTable('item_types')) {
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 255);
            $table->TEXT('type_description', 255)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });

         // Insert data into the table
         DB::table('item_types')->insert([
            ['type_name' => 'CONSUMABLE', 'type_description' => 'Consumable items are goods that are intended to be consumed. This category includes goods such as food, beverages, and other items that are used up quickly.', 'status' => 'ACTIVE'],
            ['type_name' => 'FIXED ASSET', 'type_description' => 'As per company policy, all assets with a useful life of over one year and a cost greater than Php10,000 should be capitalized as fixed assets. Fixed assets are recorded on the balance sheet and then depreciated over the useful life of the asset.', 'status' => 'ACTIVE'],
            ['type_name' => 'RAW MATERIAL', 'type_description' => 'Raw materials are materials or substances used in the primary production or manufacturing of goods. Raw materials are often referred to as commodities, which are bought and sold on commodities exchanges worldwide.', 'status' => 'ACTIVE'],
            ['type_name' => 'SERVICE', 'type_description' => 'A type of economic activity that is intangible, is not stored, and does not result in ownership. A service is consumed at the point of sale.', 'status' => 'ACTIVE'],
            ['type_name' => 'WORK IN PROGRESS', 'type_description' => 'Work in progress (WIP) refers to a component of a company\'s inventory that is partially completed. The value of that partially completed inventory is sometimes also called goods in process on the balance sheet.', 'status' => 'ACTIVE'],
            ['type_name' => 'FINISHED GOODS', 'type_description' => 'Finished goods are goods that have completed the manufacturing process but have not yet been sold or distributed to the end user.', 'status' => 'ACTIVE'],
            ['type_name' => 'OTHERS', 'type_description' => 'Other types of items that do not fall under the other categories.', 'status' => 'ACTIVE'],
        ]);
        }

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

        });
              // Insert data into the table
              DB::table('statuses')->insert([
                ['status_name' => 'ACTIVE', 'status_description' => 'Active status which means it can be visible and used'],
                ['status_name' => 'INACTIVE', 'status_description' => 'Inactive status which means it cannot be used'],
                ['status_name' => 'REJECTED', 'status_description' => 'Rejected status which means it has been rejected and cannot be used'],
                ['status_name' => 'FOR APPROVAL', 'status_description' => 'For approval status which means it is waiting for approval'],
                ['status_name' => 'FOR REVIEW', 'status_description' => 'For review status which means it is waiting for review'],

            ]);


        }

        if (!Schema::hasTable('classifications')) {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->string('classification_name', 255)->nullable();
            $table->string('classification_description', 255)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
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
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
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
            $table->foreignId('item_type_id')->constrained('item_types')->onDelete('no action')->onUpdate('no action');
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
