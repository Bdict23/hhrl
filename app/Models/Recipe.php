<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;
use App\Models\Item;
use App\Models\PriceLevel;


class Recipe extends Model
{
    use HasFactory;
    protected $table = 'recipes';
    protected $fillable = [
            'menu_id',
            'item_id',
            'qty',
            'uom_id',
            'price_level_id',
        ];

    public function menu(){
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function price_level(){
        return $this->belongsTo(PriceLevel::class, 'price_level_id');
    }

    public function uom(){
        return $this->belongsTo(UOM::class, 'uom_id');
    }
    public function latestItemCost()
{
    return $this->hasOne(PriceLevel::class, 'item_id', 'item_id')
        ->where('price_type', 'cost')
        ->where('branch_id', auth()->user()->branch_id)
        ->latest('created_at');
}

public function conversionFactor(){
    $uom = $this->uom()->first()->id;
    return $this->item()->first()
        ->units->fromUnits
        ->where('to_uom_id',  $uom)
        ->first()->conversion_factor ?? 1;
}

//  public function getConversionFactorValue()
//         {
//             $conversion = $this->conversionFactor;
//             return $conversion ? $conversion->conversion_factor : 1; // Default to 1 if not found
//         }

}
