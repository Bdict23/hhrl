<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeCardex extends Model
{
    //
    protected $table = 'recipe_cardex';
    protected $fillable = [
        'branch_id',
        'menu_id',
        'qty_in',
        'qty_out',
        'status',
        'transaction_type',
        'adjustment_id',
        'order_id',
        'final_date',
        'created_at',
        'updated_at'
    ];

    public function adjustment()
    {
        return $this->belongsTo(InventoryAdjustment::class, 'adjustment_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
