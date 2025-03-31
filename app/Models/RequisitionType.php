<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionInfo;

class requisitionType extends Model
{
    use HasFactory;
public function requisitionInfos()
{
    //dd($this->belongsTo(requisitionInfos::class));
    return $this->hasMany(RequisitionInfo::class);

}

}
