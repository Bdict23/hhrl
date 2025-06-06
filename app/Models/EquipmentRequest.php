<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentRequest extends Model
{
    //
    protected $table = 'equipment_requests';
    protected $fillable = [
        'reference_number',
        'document_number',
        'event_id',
        'department_id',
        'event_date',
        'from_time',
        'to_time',
        'requested_by',
        'approved_by',
        'received_by',
        'status',
        'notes',
        'branch_id',
    ];
}
