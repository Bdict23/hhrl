<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\requisitionInfos;

class employees extends Model
{
    use HasFactory;


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function requisitionsPrepared()
{
    return $this->hasMany(requisitionInfos::class, 'prepared_by');
}
public function requisitionsReviewed()
{
    return $this->hasMany(requisitionInfos::class, 'reviewed_by');
}
public function requisitionsApproved()
{
    return $this->hasMany(requisitionInfos::class, 'approved_by');
}

public function signatories()
{
    return $this->hasMany(signatories::class, 'employee_id');
}

}
