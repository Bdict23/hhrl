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

        if (!Schema::hasTable('backorders')) {
            Schema::create('backorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisition_infos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['ACTIVE', 'FULLFILLED', 'FOR PO', 'CANCELLED'])->default('ACTIVE');
            $table->timestamp('cancelled_date')->nullable();
            $table->enum('bo_type', ['REQ', 'PO'])->default('REQ');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->text('remarks')->nullable();
            $table->unsignedInteger('receiving_attempt')->default(0);
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

            $table->index('bo_type');
            $table->index('status');
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
