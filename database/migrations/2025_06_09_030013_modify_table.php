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
        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->nullable()->default('ACTIVE')->change();
            $table->enum('availability', ['VACANT', 'OCCUPIED', 'RESERVED'])->nullable()->default('VACANT');
            $table->timestamp('created_at')->useCurrent()->change();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->change();
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
