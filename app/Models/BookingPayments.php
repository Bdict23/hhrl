<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayments extends Model
{
    //
    protected $fillable = [
        'OR_number',
        'payment_type',
        'amount_due',
        'amount_payed',
        'balance',
        'payment_status',
        'booking_number',
    ];

}
