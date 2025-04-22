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
        'permission',
        'read_only',
        'full_access',
        'restrict',
    ];
}
