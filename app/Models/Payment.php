<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = [
        'invoice_id', 
        'branch_id', 
        'amount', 
        'customer_id', 
        'prepared_by', 
        'status', 
        'amount', 
        'cheque_id', 
        'type', 
        'payment_type_id', 
        'created_at', 
        'updated_at', 
        'payment_parent' 
    ];

    use HasFactory;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}
