<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leisure extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'status',
        'unit',
        'branch_id'
    ];

    public function bookingService()
    {
        return $this->hasMany(BookingService::class, 'leisure_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

}
