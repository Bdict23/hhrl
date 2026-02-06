<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
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
        'customer_id',
        'order_type',
        'order_round',
        'created_at',
        'updated_at',
        'prepared_by',
    ];

    public function order_details(){
        return $this->hasMany(OrderDetail::class)->with('menu');
    }
    public function order_details_unmarked(){
        return $this->hasMany(OrderDetail::class)->where('status', 'PENDING')->with('menu');
    }

    //has many table
    public function tables(){
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function OrderDiscounts()
    {
        return $this->hasMany(OrderDiscount::class, 'order_id');
    }
    public function ordered_items()
    {
        return $this->hasMany(OrderDetail::class, 'order_id')->where('marked', true);
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }


}
