<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\menu;
use App\Models\items;
use App\Models\priceLevel;


class recipe extends Model
{
    use HasFactory;
    protected $table = 'recipes';

    public function menu(){
        return $this->belongsTo(menu::class, 'menu_id');
    }

    public function item(){
        return $this->belongsTo(items::class, 'item_id');
    }

    public function price_level(){
        return $this->belongsTo(priceLevel::class, 'price_level_id');
    }

    public function uom(){
        return $this->belongsTo(uom::class, 'uom_id');
    }

   
}
