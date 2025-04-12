<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionInfo;
use App\Models\Branch;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'corporate_id',
        'name',
        'middle_name',
        'last_name',
        'contact_number',
        'position',
        'religion',
        'birth_date',
        'status'
    ];

<<<<<<< HEAD
    protected $casts = [
        'birth_date' => 'date',
    ];
    
=======
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

>>>>>>> 21e0930d73201dc604e6f582bb099db141df5abf
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function requisitionsPrepared()
{
    return $this->hasMany(RequisitionInfo::class, 'prepared_by');
}
public function requisitionsReviewed()
{
    return $this->hasMany(RequisitionInfo::class, 'reviewed_by');
}
public function requisitionsApproved()
{
    return $this->hasMany(RequisitionInfo::class, 'approved_by');
}

public function signatories()
{
    return $this->hasMany(Signatory::class, 'employee_id');
}

public function department()
{
    return $this->belongsTo(Department::class);
}
<<<<<<< HEAD
public function companies()
{
    return $this->belongsToMany(Company::class, 'company_employees', 'emp_id', 'company_id')
                ->withPivot('department_id')
                ->withTimestamps();
}
public function branches()
{
    return $this->belongsToMany(Branch::class, 'branch_employees', 'emp_id', 'branch_id')
                ->withTimestamps();
=======
public function user()
{
    return $this->hasOne(User::class, 'emp_id');
>>>>>>> 21e0930d73201dc604e6f582bb099db141df5abf
}

}
