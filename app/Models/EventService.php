<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventService extends Model
{
    //
    protected $table = 'event_services';
    protected $fillable = [
        'event_id',
        'service_id',
        'price_id',
        'qty',
    ];

    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function price()
    {
        return $this->belongsTo(PriceLevel::class, 'price_id');
    }
    public function cost()
    {
        return $this->hasOne(PriceLevel::class, 'service_id','service_id')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('price_type', 'COST')->latest('created_at');
    }
}
