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
        Schema::create('banquet_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name')->nullable()->comment('Name of the banquet event');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the venue where the event is held');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the customer who booked the event');
            $table->date('event_date')->nullable()->comment('Date of the banquet event');
            $table->time('start_time')->nullable()->index('start_time')->comment('Start time of the banquet event');
            $table->time('end_time')->nullable()->index('end_time')->comment('End time of the banquet event');
            $table->integer('guest_count')->nullable()->comment('Number of guests expected at the event');
            $table->enum('status', ['PREPARING','PENDING', 'CONFIRMED','UNATTENDED', 'CANCELLED'])->default('PENDING')->index('status')->comment('Status of the banquet event');
            $table->text('notes')->nullable()->comment('Additional notes or special requests for the event');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the branch this event belongs to');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who created the event');
            $table->foreignId('updated_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who last updated the event');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });

        Schema::table('banquet_events', function (Blueprint $table) {
            $table->index(['venue_id', 'branch_id'], 'idx_venue_branch'); // Index for faster search by venue and branch
            $table->index(['customer_id', 'branch_id'], 'idx_customer_branch'); // Index for faster search by customer and branch
            $table->index(['event_date', 'branch_id'], 'idx_event_date_branch'); // Index for faster search by event date and branch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banquet_events');
    }
};
