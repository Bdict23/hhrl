<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLiquidation extends Model
{
    //
    protected $table = 'event_liquidations';
    protected $fillable = [
        'reference',
        'branch_id',
        'created_by',
        'event_id',
        'status',
        'updated_by',
        'purpose',
        'total_incurred',
        'created_at',
        'updated_at',
        'reviewed_by',
        'approved_by',
        'reviewed_date',
        'approved_date'
    ];

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

}
