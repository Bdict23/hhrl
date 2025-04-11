<?php

namespace App\Models;

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

    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
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
}
