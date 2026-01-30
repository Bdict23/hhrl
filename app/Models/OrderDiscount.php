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
        'type',
        'status',
        'calculated_amount',

    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }
}
