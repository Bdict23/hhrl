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

    protected $fillable = [
        'source_branch_id',
        'qty_in',
        'qty_out',
        'expiration_date',
        'manufactured_date',
        'item_id',
        'status',
        'transaction_type',
        'price_level_id',
        'invoice_id',
        'stf_id',
        'widthdrawal_id',
        'receiving_id',
        'requisition_id',
        'final_date',
    ];

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
        return $this->belongsTo(Receiving::class, 'receiving_id');
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class, 'withdrawal_id');
    }

    public function priceLevel()
    {
        return $this->belongsTo(PriceLevel::class, 'price_level_id');
    }

}
