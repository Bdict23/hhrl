<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\items;


class statuses extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->hasMany(items::class);
    }
}

