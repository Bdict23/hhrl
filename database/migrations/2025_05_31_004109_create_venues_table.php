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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('venue_name')->nullable()->comment('Name of the venue');
            $table->string('venue_code')->unique()->nullable()->comment('Unique code for the venue');
            $table->string('capacity')->nullable()->comment('Seating capacity of the venue');
            $table->string('description')->nullable()->comment('Description of the venue');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this venue belongs to');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change;
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->index(['venue_name', 'branch_id'], 'idx_venue_name_branch'); // Index for faster search by venue name and branch
            $table->index(['venue_code', 'branch_id'], 'idx_venue_code_branch'); // Index for faster search by venue code and branch
        });

        Schema::table('price_levels', function (Blueprint $table) {
            $table->foreignId('venue_id')->nullable()->constrained('venues')->onDelete('set null')->onUpdate('cascade')->comment('Reference to the venue for this price level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
