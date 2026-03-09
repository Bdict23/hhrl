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
        'acknowledgement_receipt_id',
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
        'transaction_title_id',
        'transaction_title',
    ];

    public function acknowledgementReceipt()
    {
        return $this->belongsTo(AcknowledgementReceipt::class, 'acknowledgement_receipt_id');
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
}
