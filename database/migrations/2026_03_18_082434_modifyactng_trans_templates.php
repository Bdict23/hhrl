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
        Schema::create('actng_template_names', function (Blueprint $table) {
            $table->id();
            $table->string('template_name')->index();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
                $table->boolean('is_active')->default(true)->index()->comment('Indicates whether active or not');
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
                $table->index('company_id', 'idx_template_names_company_id'); // add index for 'company_id'
                $table->index('is_active', 'idx_template_names_is_active'); // add index for 'is_active'
        });

        Schema::table('actng_trans_templates', function (Blueprint $table) {
            $table->dropColumn('template_name');
            $table->foreignId('template_name_id')->nullable()->constrained('actng_template_names')->onDelete('cascade');
             $table->index('template_name_id', 'idx_template_name_id'); // add index for 'template_name_id'
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actng_trans_templates', function (Blueprint $table) {
            $table->dropForeign(['template_name_id']);
            $table->dropIndex('idx_template_name_id');
            $table->dropColumn('template_name_id');
        });

        Schema::dropIfExists('actng_template_names');
    }
};
