<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceLevel extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
