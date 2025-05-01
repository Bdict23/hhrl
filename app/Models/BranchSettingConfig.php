<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSettingConfig extends Model
{
    //
    protected $fillable = [
        'branch_id',
        'setting_id',
        'value',
    ];
}
