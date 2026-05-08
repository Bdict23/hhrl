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
        Schema::table('requisition_infos', function ($table) {
            $table->foreignId("term")->nullable()->constrained('system_parameters')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        //
    }
};
