<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UOM extends Model
{
    use HasFactory;

    protected $table = 'unit_of_measures';

    protected $fillable = [
        'unit_name',
        'unit_symbol',
        'company_id',
        'status',
    ];

    public function fromUnits()
    {
        return $this->hasMany(UnitConversion::class,'from_uom_id');
    }

    public function toUnits()
    {
        return $this->hasMany(UnitConversion::class,'to_uom_id');
    }

    public function unitConversions()
    {
        return $this->fromUnits()->merge($this->toUnits());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function items()
    {
        return $this->hasOne(Item::class, 'uom_id');
    }
}
