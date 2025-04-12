<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceivingAttachment;
use App\Models\Requisition;

class Receiving extends Model
{
    use HasFactory;

    protected $table = 'receivings'; // Keep the table name as it is in the database
    protected $fillable = [
        'REQUISITION_ID',
        'PACKING_NUMBER',
        'RECEIVING_TYPE',
        'RECEIVING_NUMBER',
        'WAYBILL_NUMBER',
        'DELIVERY_NUMBER',
        'INVOICE_NUMBER',
        'RECEIVED_DATE',
        'remarks',
        'CHECKED_BY',
        'ALLOCATED_BY',
        'DELIVERED_BY',
        'ATTACHMENT',
        'created_by',
        'branch_id',
        'company_id',
        'receiving_status',
        'stf_id'
    ];

    public function attachments()
    {
        return $this->hasMany(ReceivingAttachment::class);
    }

    public function requisition()
    {
        return $this->belongsTo(RequisitionInfo::class, 'REQUISITION_ID');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'BRANCH_ID');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'COMPANY_ID');
    }

    public function preparedBy()
    {
        return $this->belongsTo(Employee::class, 'PREPARED_BY');
    }

    public function cardex()
    {
        return $this->hasMany(Cardex::class, 'RECEIVING_ID');
    }
}
