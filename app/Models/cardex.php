<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\items;
use App\Models\Branch;
use App\Models\stocktransfer_info;
use App\Models\receiving;


class cardex extends Model
{
    use HasFactory;
    protected $table = 'cardex';

    public function item()
    {
        return $this->belongsTo(items::class, 'item_id');
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
