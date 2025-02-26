<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class priceLevel extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->belongsTo(items::class, 'items_id');
    }

    public function supplier()
    {
        return $this->belongsTo(supplier::class);
    }
}
