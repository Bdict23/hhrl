<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PriceLevel;
use App\Models\Location;
use App\Models\UOM;
use App\Models\ItemType;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_description',
        'item_barcode',
        'company_id',
        'classification_id',
        'sub_class_id',
        'brand_id',
        'category_id',
        'uom_id',
        'created_by',
    ];

    public function priceLevel()
    {
        return $this->hasMany(PriceLevel::class, 'item_id');
    }
    public function costPrice()
    {
        return $this->hasOne(PriceLevel::class, 'item_id')->where('price_type', 'Cost')->latest();
    }
    public function sellingPrice()
    {
        return $this->hasOne(PriceLevel::class, 'item_id')->where('price_type', 'SRP')->latest();
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

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }
}
