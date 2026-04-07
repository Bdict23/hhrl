<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventVenue extends Model
{
    //
    protected $table = 'event_venues';
    protected $fillable = [
        'event_id',
        'venue_id',
        'qty',
        'price_id',
        'total_amount',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
    ];


    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
    public function ratePrice()
    {
        return $this->belongsTo(PriceLevel::class, 'price_id');
    }
    public function price(){
        return $this->belongsTo(PriceLevel::class, 'price_id');
    }
}
