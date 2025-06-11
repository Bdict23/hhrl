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
        Schema::table('menus', function (Blueprint $table) {
            $table->enum('recipe_type', ['Banquet', 'Ala carte'])->nullable()->default('Ala carte');
        });
        Schema::create('branch_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('menu_id')->constrained()->references('id')->on('menus')->onDelete('cascade')->onUpdate('cascade');
            $table->string('control_name')->nullable();
            $table->boolean('is_available')->nullable()->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('mon')->nullable()->default(false);
            $table->boolean('tue')->nullable()->default(false);
            $table->boolean('wed')->nullable()->default(false);
            $table->boolean('thu')->nullable()->default(false);
            $table->boolean('fri')->nullable()->default(false);
            $table->boolean('sat')->nullable()->default(false);
            $table->boolean('sun')->nullable()->default(false);
            $table->foreignId('created_by')->nullable()->constrained('employees')->references('id')->on('employees')->onDelete('set null')->onUpdate('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_menus');
    }
};
