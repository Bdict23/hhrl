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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_forsale')->default(false)->after('uom_id')->comment('indicates if item is for sale');
            $table->enum('measurement_type', ['WEIGHT', 'UNIT','VOLUME'])->default('UNIT')->after('is_forsale')->comment('measurement type for items sold by weight/volume or unit');
            $table->decimal('optimal_stock', 10, 2)->default(0.00)->after('measurement_type')->comment('optimal stock in base unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('is_forsale');
            $table->dropColumn('measurement_type');
            $table->dropColumn('optimal_stock');
        });
    }
};
