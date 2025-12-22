<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Customer;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'customer_id',
        'updated_by',
        'status',
        'prepared_by',
        'amount',
        'payment_mode',
        'order_id',
        'customer_name',
        'branch_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


}
