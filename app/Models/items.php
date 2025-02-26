<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\priceLevel;
use App\Models\statuses;
use App\Models\locations;
use App\Models\uom;

class items extends Model
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
        return $this->hasMany(priceLevel::class);
    }

    public function statuses()
    {
        return $this->belongsTo(statuses::class);
    }

    public function locations()
    {
        return $this->hasMany(locations::class);
    }

    public function units()
    {
        return $this->belongsTo(uom::class, 'uom_id');
    }
}
