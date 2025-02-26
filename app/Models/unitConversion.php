<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\uom;

class unitConversion extends Model
{
    use HasFactory;
    protected $table = 'unit_conversions';

    protected $fillable = [
        'from_uom_id',
        'to_uom_id',
        'conversion_factor',
    ];

    public function From()
    {
        return $this->belongsTo(uom::class,'from_uom_id');
    }

    public function To()
    {
        return $this->belongsTo(uom::class,'to_uom_id');
    }
}
