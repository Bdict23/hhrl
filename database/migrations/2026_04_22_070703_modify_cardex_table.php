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
        Schema::table("cardex", function (Blueprint $table) {
            $table->foreignId("batch_id")->nullable()->constrained("batch_properties")->onDelete("cascade");
            $table->string("reference")->nullable();

            //index
            $table->index("batch_id", "idx_batch_id");
            $table->index("reference", "idx_reference");

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
