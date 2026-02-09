<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    //
    protected $table = 'module_permissions';
    protected $fillable = [
        'employee_id',
        'module_id',
        'access',
        'permission',
        'read_only',
        'full_access',
        'restrict',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function hasAccess()
    {
        return $this->access === 1;
    }
}
