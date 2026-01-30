<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationOfOrder extends Model
{
    //
    protected $table = 'cancellation_of_order_logs';

    protected $fillable = [
        'order_id',
        'order_detail_id',
        'reason_code',
        'cancelled_by',
        'created_at',
        'updated_at',
        'branch_id',
    ];
}
