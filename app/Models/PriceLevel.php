<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceLevel extends Model
{

    use HasFactory;
    protected $fillable = [
        'item_id',
        'menu_id',
        'venue_id',
        'price_type',
        'markup',
        'amount',
        'start_date',
        'end_date',
        'created_by',
        'company_id',
        'supplier_id',
        'branch_id',
        'created_at',
        'updated_at',
        'service_id',
    ];
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function branchRetail()
    {
        return $this->hasOne(PriceLevel::class, 'id')
                ->whereNotNull('item_id') // item only
                ->where('price_type', '`SRP`')
                ->groupBy('item_id', 'id', 'price_type', 'branch_id', 'menu_id', 'markup')
                ->orderBy('branch_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
