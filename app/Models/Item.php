<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PriceLevel;
use App\Models\Statuse;
use App\Models\Location;
use App\Models\UOM;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category',
        'image',
        'priceLevel',
        'item_code',          // Added item_code
        'item_description',   // Added item_description
        'uom_id',             // Ensure uom_id is included for relationships
    ];

    public function priceLevel()
    {
        return $this->hasMany(PriceLevel::class, 'item_id');
    }

    public function statuses()
    {
        return $this->belongsTo(Status::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function units()
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }

    public function cardex()
    {
        return $this->hasMany(Cardex::class, 'item_id');
    }

}
