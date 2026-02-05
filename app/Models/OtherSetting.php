<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherSetting extends Model
{
    //
    protected $table = 'other_settings';
    protected $fillable = [
        'branch_id',
        'setting_key',
        'setting_value',
        'is_active',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

}
