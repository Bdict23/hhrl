<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchEmployee extends Model
{
    protected $fillable = [
        'branch_id',
        'emp_id',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
  
    public function employees()
    {
        
        return $this->belongsToMany(Employee::class, 'branch_employee', 'branch_id', 'emp_id')
                    ->withTimestamps();
    }
}
