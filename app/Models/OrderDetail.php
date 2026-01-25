<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
protected $table = 'order_details';
  protected $fillable = [
        'order_id',
        'menu_id',
        'status',
        'order_round',
        'qty',
        'price_level_id',
        'created_at',
        'updated_at',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
    public function priceLevel()
    {
        return $this->belongsTo(PriceLevel::class, 'price_level_id');
    }
    public function OrderDiscounts()
    {
        return $this->hasMany(OrderDiscount::class, 'order_detail_id');
    }
}
