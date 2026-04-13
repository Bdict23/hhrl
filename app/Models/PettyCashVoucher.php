<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCashVoucher extends Model
{
    //
    protected $table = 'petty_cash_vouchers';
    protected $fillable = [
        'branch_id',
        'company_id',
        'event_id',
        'reference',
        'voucher_number',
        'paid_to_employee_id',
        'paid_to_customer_id',
        'total_amount',
        'purpose',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'account_types_id',
        'account_type',
        'template_id',
        'transaction_title',
    ];

    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'paid_to_customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'paid_to_employee_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function pcvDetails()
    {
        return $this->hasMany(PCVDetail::class, 'petty_cash_voucher_id');
    }
    public function cashReturn()
    {
        return $this->hasOne(CashReturn::class, 'pcv_id')->where('status', 'FINAL');
    }
    public function hasCashReturn()
    {
        return $this->hasOne(CashReturn::class, 'pcv_id');
    }
    
}
