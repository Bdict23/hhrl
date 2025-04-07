<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'stf_id'
    ];

    public function attachments()
    {
        return $this->hasMany(ReceivingAttachment::class);
    }
}
