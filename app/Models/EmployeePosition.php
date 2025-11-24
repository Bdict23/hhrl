<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class EmployeePosition extends Model
{
    protected $table = 'employee_positions';
    protected $fillable = [
        'position_name',
        'position_description',
        'position_status',
        'created_at',
        'updated_at',
        'branch_id',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
