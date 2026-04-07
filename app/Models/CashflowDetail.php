<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashflowDetail extends Model
{
    //
    protected $table = 'cashflow_details';
    protected $fillable = [
        'branch_id',
        'cashflow_id',
        'account_title_id',
        'amount',
        'created_at',
        'updated_at',
    ];
}
