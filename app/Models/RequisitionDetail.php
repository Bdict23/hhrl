<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\RequisitionInfo;

class RequisitionDetail extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function requisitionInfos()
    {
        return $this->belongsTo(RequisitionInfo::class, 'requisition_info_id');
    }
}
