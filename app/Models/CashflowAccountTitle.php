<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashflowAccountTitle extends Model
{
    //
    protected $table = 'cashflow_account_titles';
    protected $fillable = [
        'branch_id',
        'company_id',
        'title',
        'description',
        'type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];


    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
}
