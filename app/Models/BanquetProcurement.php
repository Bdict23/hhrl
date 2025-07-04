<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BanquetProcurement extends Model
{
    //
    protected $table = 'banquet_procurements';

    protected $fillable = [
        'event_id',
        'document_number',
        'reference_number',
        'approved_amount',
        'suggested_amount',
        'approved_by',
        'created_by',
        'noted_by',
        'branch_id',
        'updated_by',
        'notes',
        'create_at',
        'updated_at',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
    public function notedBy()
    {
        return $this->belongsTo(Employee::class, 'noted_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
}
