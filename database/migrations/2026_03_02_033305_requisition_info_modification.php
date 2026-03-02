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
        Schema::table('requisition_infos', function (Blueprint $table) {
                //drop indexes if they exist before dropping the column
               $table->dropForeign(['order_type']); // drop the foreign key constraint
               $table->dropColumn('order_type'); // drop the 'order_type' column
               $table->decimal('total_amount', 15, 2)->default(0)->after('remarks'); // add the 'total_amount' column after 'remarks'
               $table->foreignId('production_id')->nullable()->constrained('production_orders')->after('event_id');

               $table->index('production_id', 'idx_production_id'); // add index for 'production_id'

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisition_infos', function (Blueprint $table) {
            $table->string('order_type')->nullable()->after('event_id'); // add the 'order_type' column back
            $table->dropColumn('total_amount'); // drop the 'total_amount' column
            $table->dropForeign(['production_id']); // drop the foreign key constraint
            $table->dropColumn('production_id'); // drop the 'production_id' column
        });
    }
};
