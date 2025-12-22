<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'payment_reference',
        'customer_id',
        'prepared_by',
        'status',
        'updated_by',
        'amount',
        'cheque_id',
        'type',
        'payment_type_id',
    ];

    use HasFactory;
}
