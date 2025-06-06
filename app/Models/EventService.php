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
}
