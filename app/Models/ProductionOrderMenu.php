<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderMenu extends Model
{
    //
    protected $table = 'production_order_menus';
    protected $fillable = [
        'branch_id',
        'production_order_id',
        'menu_id',
        'qty',
        'created_at',
        'updated_at',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}