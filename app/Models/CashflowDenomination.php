<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashflowDenomination extends Model
{
    //
    protected $table = 'cashflow_denominations';
    protected $fillable = [
        'cashflow_id',
        'denomination_id',
        'quantity',
        'amount',
        'created_at'
    ];
}
