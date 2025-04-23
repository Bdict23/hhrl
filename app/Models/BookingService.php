<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    //
    protected $fillable = [
        'booking_records_id',
        'leisure_id',
        'booking_payment_id',
        'amount',
        'quantity',
        'total_amount',
    ];
    public function booking_records()
    {
        return $this->belongsTo(BookingRecords::class, 'booking_records_id');
    }
    public function leisure()
    {
        return $this->belongsTo(Leisure::class, 'leisure_id');
    }

    public function booking_payment()
    {
        return $this->belongsTo(BookingPayments::class, 'booking_payment_id');
    }
}
