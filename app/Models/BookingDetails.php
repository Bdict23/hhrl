<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDetails extends Model
{
    //
    public $fillable = [
        'booking_records_id',
        'customer_category',
        'male_count',
        'female_count',
        'entrance_fee',
        'total_count',
        'total_amount',
    ];

    public function booking_records()
    {
        return $this->belongsTo(BookingRecords::class, 'booking_records_id');
    }
}
