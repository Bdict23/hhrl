<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnitConversion;

class UOM extends Model
{
    use HasFactory;
    protected $table = 'unit_of_measures';

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
}
