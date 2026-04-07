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
        Schema::create('event_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade')->index();
            $table->foreignId('discount_id')->constrained('discounts')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('banquet_events')->onDelete('cascade');
            $table->foreignId('event_menu_id')->nullable()->constrained('event_menus')->onDelete('cascade');
            $table->foreignId('event_service_id')->nullable()->constrained('event_services')->onDelete('cascade');
            $table->foreignId('event_venue_id')->nullable()->constrained('event_venues')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('type', ['SINGLE', 'WHOLE'])->default('SINGLE')->index();
            $table->enum('status', ['APPLIED', 'CANCELLED'])->default('APPLIED')->index();
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change


            $table->index('branch_id', 'idx_branch_id');
            $table->index('discount_id', 'idx_discount_id');
            $table->index('event_id', 'idx_event_id');
            $table->index('event_menu_id', 'idx_event_menu');
            $table->index('event_service_id', 'idx_event_service');
            $table->index('event_venue_id', 'idx_event_venue');
            $table->index('created_by', 'idx_created_by');
            $table->index('status', 'idx_status');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_discounts');
    }
};
