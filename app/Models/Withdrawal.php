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
        'withdrawal_type',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

    public function preparedBy()
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }

    public function cardex()
    {
        return $this->hasMany(Cardex::class, 'withdrawal_id');
    }
    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }
    public function getTotalPriceLevelAmountAttribute()
    {
        return $this->cardex->sum(function ($cardex) {
            return optional($cardex->priceLevel)->amount ?? 0;
        });
    }

    public function withdrawalType()
    {
        return $this->belongsTo(OtherSetting::class, 'withdrawal_type', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }
  
}
