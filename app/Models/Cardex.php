<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Branch;
use App\Models\StockTransferInfo;
use App\Models\Receiving;
use App\Models\Withdrawal;
use App\Models\PriceLevel;
use App\Models\RequisitionInfo;
use App\Models\Invoice;
use App\Models\Location;


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
    public function stockTransfer()
    {
        return $this->belongsTo(StockTransferInfo::class, 'stf_id');
    }
    public function requisition()
    {
        return $this->belongsTo(RequisitionInfo::class, 'requisition_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
   
    public function totalIn()
    {
        if (!isset($this->item_id)) {
            throw new \Exception("Cannot Calculate Total In, Item ID is not set for this Cardex instance.");
        }
        return $this->where('status', 'final')->where('item_id',$this->item_id)->sum('qty_in');
    }
    public function totalOut()
    {
        if (!isset($this->item_id)) {
            throw new \Exception("Cannot Calculate Total Out, Item ID is not set for this Cardex instance.");
        }
        return $this->where('status', 'final')->where('item_id', $this->item_id)->sum('qty_out');
    }
    public function totalBalance()
    {
        if (!isset($this->item_id)) {
            throw new \Exception("Item ID is not set for this Cardex instance.");
        }

        $totalIn = $this->where('status', 'final')->where('item_id', $this->item_id)->sum('qty_in');
        $totalOut = $this->where('status', 'final')->where('item_id', $this->item_id)->sum('qty_out');
        return $totalIn - $totalOut;
    }
    

  


}
