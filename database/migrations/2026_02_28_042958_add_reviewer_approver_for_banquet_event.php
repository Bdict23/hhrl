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
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->after('status')->constrained('employees')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->after('reviewer_id')->constrained('employees')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('approver_id');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('banquet_events', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropForeign(['approver_id']);
            $table->dropColumn(['reviewer_id', 'approver_id', 'reviewed_at', 'approved_at']);
        });
    }
};
