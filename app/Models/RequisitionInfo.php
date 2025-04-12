<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\RequisitionDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\User;
use App\Models\Term;
use App\Models\Item;


class requisitionInfo extends Model
{
    //protected $primaryKey = 'requisition_number';
    use HasFactory;

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }
    public function requisitionDetails()
    {
        return $this->hasMany(RequisitionDetail::class, 'requisition_info_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function preparer()
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }
}
