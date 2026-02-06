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
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->comment('Reference number or description for the inventory adjustment');
            $table->enum('adjustment_type', ['INCREASE', 'DECREASE']);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade')->onUpdate('no action');
            $table->enum('status', ['DRAFT','PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->enum('category', ['ITEM', 'RECIPE'])->default('ITEM');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->enum('reason', ['DAMAGED', 'EXPIRED', 'LOSS', 'EXCESS','INVENTORY_COUNT']);
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
         
        });

        // add inv_adj_id to cardex table
        Schema::table('cardex', function (Blueprint $table) {
            $table->foreignId('adjustment_id')->nullable()->default(null)->constrained('inventory_adjustments')->onDelete('cascade')->onUpdate('no action')->after('requisition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
