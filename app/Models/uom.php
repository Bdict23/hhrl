<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\unitConversion;

class uom extends Model
{
    use HasFactory;
    protected $table = 'unit_of_measures';

    public function fromUnits()
    {
        return $this->hasMany(unitConversion::class,'from_uom_id');
    }

    public function toUnits()
    {
        return $this->hasMany(unitConversion::class,'to_uom_id');
    }
}
