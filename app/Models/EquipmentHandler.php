<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentHandler extends Model
{
    //
    protected $table = 'equipment_handlers';
    protected $fillable = [
        'employee_id',
        'equipment_request_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
