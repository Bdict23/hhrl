<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanquetEvent extends Model
{
    protected $table = 'banquet_events';
    protected $fillable = [
        'event_name',
        'reference',
        'event_address',
        'start_date',
        'end_date',
        'arrival_time',
        'departure_time',
        'guest_count',
        'status',
        'reviewer_id',
        'approver_id',
        'reviewed_at',
        'approved_at',
        'created_by',
        'notes',
        'customer_id',
        'total_amount',
    ];

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

    public function eventVenues()
    {
        return $this->hasMany(EventVenue::class, 'event_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

}
