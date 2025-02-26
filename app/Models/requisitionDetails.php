<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\items;
use App\Models\requisitionInfos;

class requisitionDetails extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->belongsTo(items::class, 'item_id');
    }

    public function requisitionInfos()
    {
        return $this->belongsTo(requisitionInfos::class, 'requisition_info_id'); 
    }
}
