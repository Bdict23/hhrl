<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    //
    protected $table = 'inventory_adjustments';

    protected  $fillable = [
        'adjustment_type',
        'branch_id',
        'status',
        'created_by',
        'approved_by',
        'reason',
        'created_at',
        'updated_at',

    ];
}
