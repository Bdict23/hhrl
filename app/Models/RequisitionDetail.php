<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\RequisitionInfo;

class RequisitionDetail extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function requisitionInfos()
    {
        return $this->belongsTo(RequisitionInfo::class, 'requisition_info_id');
    }
    public function cost()
    {
        return $this->belongsTo(PriceLevel::class, 'price_level_id');
    }

    public function backOrder(){
        return $this->hasMany(Backorder::class,'item_id','item_id');
    }
    public function totalAmount()
    {
        return $this->qty * $this->cost->amount;
    }

}
