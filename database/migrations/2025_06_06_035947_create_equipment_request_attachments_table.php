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
        Schema::create('equipment_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_request_id')->constrained()->references('id')->on('equipment_requests')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the equipment request this attachment belongs to');
            $table->string('file_path')->comment('Path to the attachment file');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_request_attachments');
    }
};
