<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $fillable = [
        'department_name',
        'department_description',
        'branch_id',
        'department_status'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
