<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;


class Status extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}

