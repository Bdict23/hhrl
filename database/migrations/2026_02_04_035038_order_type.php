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
        Schema::create('other_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('no action');
            $table->string('setting_title')->nullable()->comment('This will be used for referencing the setting in the UI');
            $table->string('setting_value')->nullable()->comment('This will be used retrieving the setting value in the code');
            $table->string('setting_key')->nullable()->comment('This will be used for referencing the setting in the code');
            $table->boolean('is_active')->default(true)->comment('Indicates whether the setting is active or not');
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });

        Schema::table('requisition_infos', function (Blueprint $table) {
            $table->foreignId('order_type')->nullable()->constrained('other_settings')->onDelete('set null')->onUpdate('no action')->after('branch_id');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreignId('withdrawal_type')->nullable()->constrained('other_settings')->onDelete('set null')->onUpdate('no action')->after('branch_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('other_settings');
        Schema::table('requisition_infos', function (Blueprint $table) {
            $table->dropForeign(['order_type']);
            $table->dropColumn('order_type');
        });
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['withdrawal_type']);
            $table->dropColumn('withdrawal_type');
        });
    }
};
