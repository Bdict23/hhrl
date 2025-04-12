<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionInfo;

class Term extends Model
{
    use HasFactory;
protected $table = 'terms';

protected $fillable = [
    'term_name',
    'term_description',
    'payment_days',
];

public function requisitionInfos()
{
    //dd($this->belongsTo(requisitionInfos::class));
    return $this->hasMany(RequisitionInfo::class);
}

}
