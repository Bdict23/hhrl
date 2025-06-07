<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentCardex extends Model
{
    protected $table = 'department_cardex';

    protected $fillable = [
        'department_id',
        'branch_id',
        'item_id',
        'qty_in',
        'qty_out',
        'equipment_request_id',
        'equipment_return_id',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }
}
