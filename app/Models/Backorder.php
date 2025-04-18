<?php

namespace App\Models;
use App\Models\RequisitionInfo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backorder extends Model
{
    use HasFactory;
    //protected $table = 'backorder';

    protected $fillable = [
        'requisition_id',
        'item_id',
        'status',
        'cancelled_date',
        'bo_type',
        'remarks',
        'branch_id',
        'company_id',
        'receiving_attempt',

    ];

    public function requisitionInfo()
    {
        return $this->belongsTo(RequisitionInfo::class,'requisition_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function cardex(){
        return $this->hasMany(Cardex::class, 'requisition_id');
    }
}
