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
        Schema::create('department_cardex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the department this cardex belongs to');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this cardex belongs to');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the item in the cardex');
            $table->integer('qty_in')->default(0)->comment('Current quantity of the item received into the cardex');
            $table->integer('qty_out')->default(0)->comment('Current quantity of the item issued from the cardex');
            $table->foreignId('equipment_request_id')->constrained('equipment_requests')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the equipment request related to this cardex');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
            $table->index(['department_id', 'branch_id'], 'idx_department_branch'); // Index for faster search by department and branch
            $table->index(['item_id', 'branch_id'], 'idx_item_branch'); // Index for faster search by item and branch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_cardex');
    }
};
