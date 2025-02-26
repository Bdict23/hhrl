<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\requisitionDetails;
use App\Models\supplier;
use App\Models\employees;
use App\Models\User;
use App\Models\requisitionTypes;
use App\Models\items;


class requisitionInfos extends Model
{
    //protected $primaryKey = 'requisition_number';
    use HasFactory;

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }
    public function requisitionDetails()
    {
        
        return $this->hasMany(requisitionDetails::class, 'requisition_info_id'); 
    }

    public function supplier()
    {
        return $this->belongsTo(supplier::class, 'supplier_id');
    }
    public function preparer()
    {
        return $this->belongsTo(employees::class, 'prepared_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(employees::class, 'reviewed_by');
    }
    public function approver()
    {
        return $this->belongsTo(employees::class, 'approved_by');
    }

    public function requisitionTypes()
    {
        return $this->belongsTo(requisitionTypes::class, 'requisition_types_id');
    }
}