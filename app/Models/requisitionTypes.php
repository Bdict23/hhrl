<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\requisitionInfos;

class requisitionTypes extends Model
{
    use HasFactory;
public function requisitionInfos()
{
    //dd($this->belongsTo(requisitionInfos::class));
    return $this->hasMany(requisitionInfos::class);

}

}
