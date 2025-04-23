<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRecords extends Model
{
    //
    protected $fillable = [
        'booking_number',
        'customer_id',
        'branch_id',
        'booking_status',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function bookingPayments()
    {
        return $this->hasMany(BookingPayments::class, 'booking_number', 'booking_number');
    }
    public function bookingService()
    {
        return $this->hasMany(BookingService::class, 'booking_records_id');
    }
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetails::class, 'booking_records_id');
    }

}
