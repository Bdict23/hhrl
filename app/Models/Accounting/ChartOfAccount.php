<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    //
    protected $table = 'actng_chart_of_accounts';
    protected $fillable = [
        'company_id',
        'parent',
        'account_code',
        'account_title',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
