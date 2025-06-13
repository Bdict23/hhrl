<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order_detail;
use App\Models\Table;

class Order extends Model
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
        'branch_id',
        'sales_rep_id',
        'customer_id',
        'order_type',
    ];

    public function order_details(){
        return $this->hasMany(Order_detail::class)->with('menu');
    }

    //has many table
    public function tables(){
        return $this->belongsTo(Table::class, 'table_id');
    }


}
