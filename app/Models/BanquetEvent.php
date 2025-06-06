<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanquetEvent extends Model
{
    protected $table = 'banquet_events';
    protected $fillable = [
        'event_name',
        'event_date',
        'start_time',
        'end_time',
        'venue_id',
        'guest_count',
        'notes',
        'customer_id',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
