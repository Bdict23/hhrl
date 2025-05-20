<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedBranch extends Model
{
    protected $table = 'assigned_branches';
    protected $fillable = [
        'employee_id',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
