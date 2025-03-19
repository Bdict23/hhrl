<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    //
    protected $table = 'withdrawals';

    protected $fillable = [
        'reference_number',
        'source_branch_id',
        'department_id',
        'usage_date',
        'useful_date',
        'approved_by',
        'reviewed_by',
        'prepared_by',
        'remarks',
    ];
}
