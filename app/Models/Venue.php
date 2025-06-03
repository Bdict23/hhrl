<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    //
    protected $table = 'venues';
    protected $fillable = [
        'venue_name',
        'venue_code',
        'capacity',
        'description',
        'status',
        'branch_id',
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    public function ratePrice()
    {
        return $this->hasOne(PriceLevel::class, 'venue_id')->where('price_type', 'RATE')->latest();
    }
}
