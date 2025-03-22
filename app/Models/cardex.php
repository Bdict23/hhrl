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
        'expiration_date',
        'manufactured_date',
        'transaction_type',
        'price_level_id',
        'invoice_id',
        'withdrawal_id',
        'stf_id',
        'receiving_id',
        'requisition_id',
        'item_id',
        'qty_in',
        'qty_out',
        'status',
        'created_at',
        'updated_at',
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
