<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\order;
use App\Models\customer;

class invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';

    public function order()
    {
        return $this->belongsTo(order::class, 'order_id');
    }

    public function customers()
    {
        return $this->belongsTo(customer::class, 'customer_id');
    }


}
