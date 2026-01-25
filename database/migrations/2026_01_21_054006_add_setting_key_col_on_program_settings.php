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
        Schema::table('program_settings', function (Blueprint $table) {
            $table->string('setting_key')->nullable()->after('description')->comment('to store unique key for each setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('program_settings', function (Blueprint $table) {
            $table->dropColumn('setting_key');
        });
    }
};
