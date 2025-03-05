<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Stocktransfer_info;
use App\Models\Receiving;


class Cardex extends Model
{
    use HasFactory;
    protected $table = 'cardex';

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function receiving()
    {
        return $this->belongsTo(receiving::class, 'receiving_id');
    }

}
