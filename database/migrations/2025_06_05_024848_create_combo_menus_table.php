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
        Schema::create('combo_recipe', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the combo menu');
            $table->text('description')->nullable()->comment('Description of the combo menu');
            $table->foreignId('company_id')->constrained()->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the company this combo menu belongs to');
            $table->foreignId('menu_id')->constrained()->references('id')->on('menus')->onDelete('cascade')->onUpdate('cascade')->comment('Reference to the main menu item for this combo');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who created the combo menu');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who approved the combo menu');
            $table->foreignId('reviewed_by')->nullable()->constrained('employees')->onDelete('set null')->onUpdate('cascade')->comment('Employee who reviewed the combo menu');
            $table->timestamp('approve_date')->nullable()->comment('Date when the combo menu was approved');
            $table->timestamp('reviewed_date')->nullable()->comment('Date when the combo menu was reviewed');
            $table->timestamp('rejected_date')->nullable()->comment('Date when the combo menu was rejected');
            $table->enum('status', ['DRAFT', 'FOR_APPROVAL', 'FOR_REVIEW', 'REJECTED', 'ACTIVE', 'INACTIVE'])->nullable()->default('DRAFT')->comment('Status of the combo menu');
            $table->text('notes')->nullable()->comment('Additional notes or comments regarding the combo menu');
            $table->timestamp('created_at')->useCurrent()->index('created_at'); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });

        Schema::table('branch_menus', function (Blueprint $table) {
            $table->foreignId('combo_recipe_id')->nullable()->constrained('combo_recipe')->onDelete('set null')->onUpdate('cascade')->comment('Reference to the combo menu, if applicable');
        });

        Schema::table('price_levels', function (Blueprint $table) {
            $table->foreignId('combo_recipe_id')->nullable()->constrained('combo_recipe')->onDelete('set null')->onUpdate('cascade')->comment('Reference to the combo menu, if applicable');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_menus');
    }
};
