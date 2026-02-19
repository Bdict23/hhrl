<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    //
    protected $table = 'production_orders';
    protected  $fillable = [
        'reference',
        'branch_id',
        'status',
        'prepared_by',
        'notes',
        'created_at',
        'updated_at',

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }
    public function productionMenus()
    {
        return $this->hasMany(ProductionOrderMenu::class, 'production_order_id');
    }
    public function productionOrderDetails()
    {
        return $this->hasMany(ProductionOrderDetail::class, 'production_order_id');
    }
}
