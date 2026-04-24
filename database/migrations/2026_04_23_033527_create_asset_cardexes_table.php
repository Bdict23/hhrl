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
        Schema::create('asset_cardex', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->foreignId('batch_dtl_id')->constrained('batch_property_details')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('qr_code')->unique();
            $table->integer('qty')->default(1);
            $table->boolean('is_serialized')->default(false);
            $table->enum('status', ['ACTIVE', 'CANCELLED'])->default('ACTIVE');
            $table->enum('type', ['IN', 'OUT'])->default('IN');
            $table->enum('transaction', ['RECEIVE', 'TRANSFERED','ADJUSTMENT'])->default('ADJUSTMENT');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index("branch_id", "idx_branch_id");
            $table->index("reference", "idx_reference");
            $table->index("item_id", "idx_item_id");
            $table->index("qr_code", "idx_qr_code");
            $table->index("is_serialized", "idx_serialized");
            $table->index("status", "idx_status");
            $table->index("transaction", "idx_transaction");
        });

        Schema::table('batch_property_details', function (Blueprint $table) {
            $table->integer('qty')->default(1);
            $table->boolean('is_serialized')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_cardex');
    }
};
