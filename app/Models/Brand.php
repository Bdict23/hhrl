<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    protected $fillable = [
        'brand_name',
        'brand_code',
        'status',
    ];

    protected $table = 'brands';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
