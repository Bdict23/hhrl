<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionInfo;

class Employee extends Model
{
    use HasFactory;


    public function branch()
    {
        return $this->belongsTo(Branch::class);
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

}
