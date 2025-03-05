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

class InventoryAdjustmentController extends Controller
{
    //Raw Material Request
    public function NewRMR(){
        $suppliers = Supplier::where('supp_status', 'ACTIVE')->get();
        $types =  RequisitionType::all();
        $items = Item::with('priceLevel','statuses')->get();
        $approver = Signatory::where('signatory_type', 'APPROVER')->get();
        $reviewer = Signatory::where('signatory_type', 'REVIEWER')->get();
         return view('inventory.raw_materials_requisition', compact('suppliers','types','items','approver','reviewer'));
        // return statuses::all();
     }
}
