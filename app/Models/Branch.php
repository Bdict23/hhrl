<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\requisitionInfos;
use App\Models\employees;
use App\Models\Company;

class branch extends Model
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
        return $this->hasMany(employees::class);
    }

    public function requisitionInfos()
    {
        return $this->hasMany(requisitionInfos::class, 'from_branch_id');
    }


    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }
 
    public function signatories()
    {
        return $this->hasMany(signatories::class, 'branch_id');
    }
    
}
