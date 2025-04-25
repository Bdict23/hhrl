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
        'position_id',
        'branch_id',
        'department_id',
        'contact_number',
        'status',
        'birth_date'
    ];
    protected $casts = [
        'birth_date' => 'date',
    ];
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

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
public function user()
{
    return $this->hasOne(User::class, 'emp_id');
}

}
