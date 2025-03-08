<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\RequisitionType;
use App\Models\Item;
use App\Models\PriceLevel;
use App\Models\Status;
use App\Models\Signatory;
use App\Models\Cardex;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    //Raw Material Request
    public function NewItemWithdrawal(){
        $suppliers = Supplier::where('supp_status', 'ACTIVE')->get();
        $types =  RequisitionType::all();
        $items = Item::with('priceLevel','statuses','item_type')->get();
        $approver = Signatory::where('signatory_type', 'APPROVER')->get();
        $reviewer = Signatory::where('signatory_type', 'REVIEWER')->get();
        $cardexBalance = Cardex::select('item_id', DB::raw('SUM(qty_in) - SUM(qty_out) as inventory_qty'))
            ->where('status', 'FINAL')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->groupBy('item_id')
            ->pluck('inventory_qty', 'item_id');
        $cardexAvailable = Cardex::select('item_id', DB::raw('SUM(qty_in) - SUM(qty_out) as available_qty'))
            ->where(function($query) {
                $query->where('status', 'RESERVED')
                      ->orWhere('status', 'FINAL');
            })
            ->where('source_branch_id', auth()->user()->branch_id)
            ->groupBy('item_id')
            ->pluck('available_qty', 'item_id');

        return view('inventory.item_withdrawal', compact('suppliers','types','items','approver','reviewer', 'cardexBalance', 'cardexAvailable'));
    }
}
