<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\order_details;
use App\Models\table;

class order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'order_status',
        'customer_name',
        'payment_status',
        'total_order',
        'total_price',
        'table_id',
    ];

    public function order_details(){
        return $this->hasMany(order_details::class)->with('menu');
    }

    //has many table
    public function tables(){
        return $this->belongsTo(table::class, 'table_id');
    }


}
