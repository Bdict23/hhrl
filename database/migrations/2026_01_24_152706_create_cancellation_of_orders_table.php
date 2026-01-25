<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // create cancelation logs table
        Schema::create('cancellation_of_order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('canceled_by')->nullable()->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->string('reason_code')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });

        // payments table modification
         Schema::table('payments', function (Blueprint $table) {
         $table->foreignId('payment_parent')->nullable()->constrained('payments')->comment('for refund back tracking')->onDelete('no action')->onUpdate('no action');

         });
        DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('DOWNPAYMENT','SERVICE','SALES','REFUND','VOID','OTHERS') NOT NULL DEFAULT 'SALES'");

         // add adjusted amount column on invoices table
         Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('adjusted_amount', 15, 2)->default(0.00);
         });

         // add is_prepared on order_details table
            Schema::table('order_details',function(Blueprint $table){
                $table->boolean('marked')->default(0)->comment('to indicate if the order detail is already paid')->change();
                $table->boolean('is_prepared')->default(0)->after('marked')->comment('to indicate if the order detail is prepared');
            });

        // create refund table logs

        


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_of_order_logs');
        DB::statement("ALTER TABLE payments MODIFY COLUMN type ENUM('DOWNPAYMENT','SERVICE','SALES','OTHERS') NOT NULL DEFAULT 'SALES'");
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_parent']);
            $table->dropColumn('payment_parent');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('adjusted_amount');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('marked');
            $table->dropColumn('is_prepared');
        });
    }
};
