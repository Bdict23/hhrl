<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PriceLevel;
use App\Models\Statuse;
use App\Models\Location;
use App\Models\UOM;
use App\Models\ItemType;

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

    public function item_type()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class, 'classification_id');
    }

    public function sub_classification()
    {
        return $this->belongsTo(Classification::class, 'sub_class_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
