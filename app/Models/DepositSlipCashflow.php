<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositSlipCashflow extends Model
{
    //
    protected $table = 'deposit_slip_cashflows';
    protected $fillable = [
        'branch_id',
        'deposit_slip_validation_id',
        'cashflow_id',
        'created_at',
        'updated_at',
    ];
}
