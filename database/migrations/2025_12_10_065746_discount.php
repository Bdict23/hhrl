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

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('type', ['SINGLE', 'WHOLE'])->default('WHOLE')->index('type')->comment('Type of discount: SINGLE for single item, WHOLE for whole order');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('code')->nullable();
            $table->boolean('auto_apply')->nullable()->index('auto_apply')->default(false);
            $table->decimal('amount', 19, 2)->default(0);
            $table->integer('percentage')->default(0)->index('percentage');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE')->index('status');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
