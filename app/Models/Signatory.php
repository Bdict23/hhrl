<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'signatory_name',
        'signatory_position',
        'signatory_email',
        'signatory_contact',
        'signatory_type',
        'signatory_status',
        'signatory_branch',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

}
