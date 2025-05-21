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
        Schema::create('program_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name')->unique()->comment('Name of the program setting');
            $table->string('description')->nullable()->comment('Description of the program setting');
            $table->timestamps();

            
        });

        // DB::table('program_settings')->insert([
        //     ['setting_name' => 'Allow Reviewer on Purchase Order', 'description' => 'Enable reviewer functionality for purchase orders', 'created_at' => now(), 'updated_at' => now()],
        //     ['setting_name' => 'Allow Reviewer on Withdrawal', 'description' => 'Enable reviewer functionality for withdrawals', 'created_at' => now(), 'updated_at' => now()],
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_settings');
    }
};
