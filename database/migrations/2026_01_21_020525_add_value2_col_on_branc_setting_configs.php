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
        Schema::table('branch_setting_configs', function (Blueprint $table) {
            $table->string('value2')->nullable()->after('value')->comment('to store additional config value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('branch_setting_configs', function (Blueprint $table) {
            $table->dropColumn('value2');
        });
    }
};
