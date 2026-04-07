<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashReturn extends Model
{
    //
    protected $table = 'cash_returns';
    protected $fillable = [
        'branch_id',
        'reference',
        'status',
        'pcv_id',
        'event_id',
        'prepared_by',
        'updated_by',
        'amount_returned',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function preparedBy()
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }
    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id', 'id');
    }
    public function pettyCashVoucher()
    {
        return $this->belongsTo(PettyCashVoucher::class, 'pcv_id', 'id');
    }
}
