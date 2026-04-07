<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositSlipValidation extends Model
{
    //
    protected $table = 'deposit_slip_validations';
    protected $fillable = [
        'branch_id',
        'bank_account_id',
        'reference_number',
        'cash_amount',
        'check_amount',
        'is_picked_up',
        'cashflow_start_date',
        'cashflow_end_date',
        'deposit_date',
        'deposited_by',
        'validated_by',
        'created_by',
        'validation_status',
        'created_at',
        'updated_at',
    ];
}
