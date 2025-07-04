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
    public function equipmentRequests()
    {
        return $this->hasMany(EquipmentRequest::class, 'event_id');
    }
    public function eventServices()
    {
        return $this->hasMany(EventService::class, 'event_id');
    }
    public function eventMenus()
    {
        return $this->hasMany(EventMenu::class, 'event_id');
    }
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'event_id');
    }
    public function purchaseOrders()
    {
        return $this->hasMany(RequisitionInfo::class, 'event_id');
    }

}
