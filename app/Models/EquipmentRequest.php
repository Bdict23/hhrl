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


    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }
    public function incharge()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
    public function attachments()
    {
        return $this->hasMany(EquipmentRequestAttachment::class, 'equipment_request_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function equipmentHandlers()
    {
        return $this->hasMany(EquipmentHandler::class, 'equipment_request_id');
    }
    public function departmentCardex()
    {
        return $this->hasMany(DepartmentCardex::class, 'equipment_request_id');
    }
    public function requestedBy()
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }
}
