<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawer extends Model
{
    //
    use HasFactory;
    protected $table = 'cash_drawers';
    protected $fillable = [
        'branch_id',
        'drawer_name',
        'drawer_code',
        'department_id',
        'drawer_status',
        'created_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

}
