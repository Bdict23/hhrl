<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Customer;

class invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


}
