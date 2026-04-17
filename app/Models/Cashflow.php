<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    //
    protected $table = 'cashflows';
    protected $fillable = [
        'branch_id',
        'reference',
        'status',
        'created_by',
        'amount',
        'approver_id',
        'notes',
        'remarks',
        'created_at',
        'updated_at',
        'beginning_balance',
        'ending_balance',
        'flow_type',
        'fund_status',
        'parent_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    public function denomination()
    {
        return $this->hasMany(CashflowDenomination::class, 'cashflow_id');
    }
    public function title()
    {
        return $this->hasMany(CashflowDetail::class, 'cashflow_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
