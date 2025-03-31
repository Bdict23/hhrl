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


}
