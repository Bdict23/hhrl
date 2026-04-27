<?php

namespace App\Models\Accounting;

use App\Models\Accounting\COATemplateName;
use App\Models\Accounting\AccountType;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class COATransactionTemplate extends Model
{
    //
    protected $table = 'actng_trans_templates';
    protected $fillable = [
        'company_id',
        'template_name_id',
        'description',
        'transaction_type',
        'module_type',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'status',
        'approved_by',
        'reviewed_by',
        'reviewed_date',
        'approved_date',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(COATransactionTemplateDetail::class, 'template_id');
    }

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'transaction_type');
    }

    public function templateName()
    {
        return $this->belongsTo(COATemplateName::class, 'template_name_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
