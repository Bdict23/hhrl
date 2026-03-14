<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancesForLiquidation extends Model
{
    //
    protected $table = 'advance_liquidations';
    protected $fillable = [
        'branch_id',
        'company_id',
        'reference',
        'status',
        'prepared_by',
        'received_by',
        'date_received',
        'date_returned',
        'approved_by',
        'updated_by',
        'amount_received',
        'amount_returned',
        'created_at',
        'updated_at',
        'notes',
    ];


    public function disburser(){
        return $this->belongsTo(Employee::class, 'received_by');
    }
    public function approver(){
        return $this->belongsTo(Employee::class, 'approved_by');
    }
    public function preparer(){
        return $this->belongsTo(Employee::class, 'prepared_by');
    }
}
