<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionInfo;
use App\Models\Employee;
use App\Models\Company;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name',
        'branch_address',
        'branch_email',
        'branch_code',
        'branch_type',
        'company_id',
        'branch_cell',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function requisitionInfos()
    {
        return $this->hasMany(RequisitionInfo::class, 'from_branch_id');
    }


    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function signatories()
    {
        return $this->hasMany(Signatory::class, 'branch_id');
    }

}
