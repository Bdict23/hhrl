<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcknowledgementReceipt extends Model
{
    //
    protected $table = 'acknowledgement_receipts';
    protected $fillable = [
        'branch_id',
        'company_id',
        'event_id',
        'reference',
        'customer_id',
        'status',
        'check_number',
        'check_amount',
        'check_date',
        'bank_id',
        'account_name',
        'amount_in_words',
        'check_status',
        'created_by',
        'updated_by',
        'notes',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    public function event()
    {
        return $this->belongsTo(BanquetEvent::class, 'event_id');
    }

    public function pettyCashVouchers()
    {
        return $this->hasMany(PettyCashVoucher::class, 'acknowledgement_receipt_id');
    }

}
