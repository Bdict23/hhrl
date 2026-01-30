<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemWasteLog extends Model
{
    //
    protected $table = 'item_waste_logs';
    protected $fillable = [
        'branch_id',
        'order_detail_id',
        'cancellation_id',
        'waste_cost',
        'price_level_cost',
        'waste_selling_price',
        'price_level_srp',
        'created_at',
        'updated_at',
        ];
}
