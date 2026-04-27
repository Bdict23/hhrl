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
        //
        Schema::table("actng_trans_templates", function (Blueprint $table) {
            $table->enum("status", ['DRAFT', 'FINAL'])->default('DRAFT');
            $table->foreignId("approved_by")->nullable()->constrained('employees')->cascadeOnDelete();
            $table->foreignId("reviewed_by")->nullable()->constrained('employees')->cascadeOnDelete();
            $table->timestamp("reviewed_date")->nullable();
            $table->timestamp("approved_date")->nullable();

            // index
            $table->index("approved_by", "idx_approved_by");
            $table->index("reviewed_by", "idx_reviewed_by");
            $table->index("approved_date", "idx_approved_date");
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
