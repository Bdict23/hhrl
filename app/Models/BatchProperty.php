<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchProperty extends Model
{
    //
    protected $table = 'batch_properties';
     protected $fillable = [
         'reference',
         'status',
         'type_id',
         'requisition_id',
         'branch_id',
         'note',
         'purpose',
         'prepared_by',
         'approved_by',
         'reviewed_by',
         'approved_date',
         'reviewed_date',
         'issued_date',
         'created_at',
         'updated_at',
     ];

    public function batchType()
    {
        return $this->belongsTo(SystemParameter::class, 'type_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

        public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function batchItems(){
        return $this->hasMany(BatchPropertyDetail::class,'batch_id');
    }
}
