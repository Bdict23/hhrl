<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class receiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'receive_from',
        'po_number',
        'merchandise_po_number',
        'date',
        'way_bill_no',
        'delivery_no',
        'invoice_no',
        'receiving_packing_no',
        'receiving_date',
        'remarks',
        'checked_by',
        'allocated_by',
        'stf_id',
    ];
}
