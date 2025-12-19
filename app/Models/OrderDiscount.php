<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    protected $table = 'order_discounts';

    protected $fillable = [
        'discount_id',
        'order_id',
        'order_detail_id',
        'created_at',
        'updated_at',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
