<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositSlipValidationCheck extends Model
{
    //
    protected $table = 'deposit_slip_validation_checks';
    protected $fillable = [
        'branch_id',
        'deposit_slip_validation_id',
        'check_id',
        'created_at',
        'updated_at',
    ];
}
