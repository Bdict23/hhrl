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
         Schema::table('categories', function (Blueprint $table) {
            DB::statement("ALTER TABLE categories MODIFY COLUMN category_type ENUM('ITEM', 'MENU','SERVICE') NOT NULL COMMENT 'Type of category: product or service'");
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->nullable()->comment('Name of the service');
            $table->string('service_code')->nullable()->index('service_code')->comment('Unique code for the service');
            $table->string('service_description')->nullable()->comment('Description of the service');
            $table->unsignedBigInteger('category_id')->comment('Foreign key to categories');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->boolean('has_multiplier')->default(false)->comment('Indicates if the service has a multiplier');
            $table->unsignedBigInteger('branch_id')->comment('Foreign key to branches');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Foreign key to employees who created the service');
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Foreign key to employees who updated the service');
            $table->foreign('updated_by')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        
        });
        Schema::table('services', function (Blueprint $table) {
            $table->index(['service_name', 'branch_id'], 'idx_service_name_branch'); // Index for faster search by service name and branch
        });
        Schema::table('price_levels', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->comment('Foreign key to services');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });

       

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
