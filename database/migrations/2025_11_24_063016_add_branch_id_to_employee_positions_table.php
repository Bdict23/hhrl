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
        Schema::table('employee_positions', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_positions', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('no action')->onUpdate('no action')->after('position_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_positions', function (Blueprint $table) {
            if (Schema::hasColumn('employee_positions', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
    }
};
