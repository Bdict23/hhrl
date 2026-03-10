<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PCVDetail extends Model
{
    //
    protected $table = 'petty_cash_voucher_details';
    protected $fillable = [
        'petty_cash_voucher_id',
        'transaction_title',
        'amount',
        'created_at',
        'updated_at',
        'transaction_title_id',
        'type',
    ];
}
