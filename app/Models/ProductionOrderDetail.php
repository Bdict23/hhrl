<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderDetail extends Model
{
    //
    protected $table = 'production_order_details';  
    protected  $fillable = [
        'production_order_id',
        'item_id',
        'menu_id',
        'qty',
        'uom_id',
        'status',
        'created_at',
        'updated_at',

    ];

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function conversionFactor(){
        $uom = $this->uom()->first()->id;
        return $this->item()->first()
            ->units->fromUnits
            ->where('to_uom_id',  $uom)
            ->first()->conversion_factor ?? 1;
    }
}
