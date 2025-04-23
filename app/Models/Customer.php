<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
     protected $fillable = [
        'customer_fname',
        'customer_lname',
        'customer_mname',
        'contact_person',
        'contact_person_relation',
        'gender',
        'contact_no_1',
        'contact_no_2',
        'customer_address',
        'email',
        'tin',
        'birthday',
        'branch_id',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    function bookingrecords()
    {
        return $this->hasMany(BookingRecords::class, 'customer_id');
    }
    function Branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');

    }
    function BookingPayments()
    {
        return $this->hasMany(BookingPayments::class, 'customers_id', 'id');
    }
    //
    public function activeBooking()
    {
        return $this->hasOne(BookingRecords::class)->where('status', 'active');
    }
}
