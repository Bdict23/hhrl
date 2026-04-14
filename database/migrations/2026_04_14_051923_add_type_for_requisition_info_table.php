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
        Schema::table('requisition_infos', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->constrained('system_parameters')->onDelete('set null')->after('id');
            // Add index for faster queries
            $table->index('type_id', 'idx_type_id');
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
