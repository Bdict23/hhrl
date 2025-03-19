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
use App\Models\Signatory;
use App\Models\Cardex;
use App\Models\Department;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;


class InventoryAdjustmentController extends Controller
{
    //Raw Material Request
    public function NewItemWithdrawal(){
        $suppliers = Supplier::where([['supp_status', 'ACTIVE'],['company_id', auth()->user()->branch->company_id]])->get();
        $types =  RequisitionType::all();
        $items = Item::with('priceLevel', 'units','category','classification')->where([['item_status', 'ACTIVE'],['company_id', auth()->user()->branch->company_id]] )->get();
        $approvers = Signatory::where([['signatory_type', 'APPROVER','employees'],['branch_id', auth()->user()->branch_id],['status', 'ACTIVE'],['MODULE', 'ITEM_WITHDRAWAL']])->get();
        $reviewers = Signatory::where([['signatory_type', 'REVIEWER','employees'],['branch_id', auth()->user()->branch_id],['status', 'ACTIVE'],['MODULE', 'ITEM_WITHDRAWAL']])->get();
        $cardexBalance = Cardex::select('item_id', DB::raw('SUM(qty_in) - SUM(qty_out) as inventory_qty'))
            ->where('status', 'FINAL')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->groupBy('item_id')
            ->pluck('inventory_qty', 'item_id');
        $cardexAvailable = Cardex::select('item_id', DB::raw('SUM(qty_in) - SUM(qty_out) as available_qty'))
            ->where(function($query) {
                $query->where([['status', 'RESERVED'],['source_branch_id', auth()->user()->branch_id]])
                      ->orWhere('status', 'FINAL');
            })
            ->where('source_branch_id', auth()->user()->branch_id)
            ->groupBy('item_id')
            ->pluck('available_qty', 'item_id');
        $categories = DB::table('categories')->select('category_name')->where([['status', 'ACTIVE'],['category_type', 'ITEM'],['company_id', auth()->user()->branch->company_id]])->get();
        $departments = Department::where([['company_id', auth()->user()->branch->company_id],['department_status', 'ACTIVE'],['branch_id', auth()->user()->branch_id]])->get();
        return view('inventory.item_withdrawal', compact('suppliers','types','items','approvers','reviewers', 'cardexBalance', 'cardexAvailable', 'categories', 'departments'));
    }


    public function storeWithdrawal(Request $request){

        try {
        $request->validate([
            'reference_number' => 'required',
            'department_id' => 'required|integer',
            'usage_date' => 'required|date',
            'useful_date' => 'nullable|date',
            'approved_to' => 'required|integer',
            'reviewed_to' => 'required|integer',
            'item_id' => 'required|array',
            'request_qty' => 'required|array',
            'cost_price' => 'required|array',
        ]);

        $withdrawal = new Withdrawal();
        $withdrawal->reference_number = $request->reference_number;
        $withdrawal->department_id = $request->department_id;
        $withdrawal->usage_date = $request->usage_date;
        $withdrawal->useful_date = $request->lifespan_date;
        $withdrawal->approved_by = $request->approved_to;
        $withdrawal->reviewed_by = $request->reviewed_to;
        $withdrawal->prepared_by = auth()->user()->emp_id;
        $withdrawal->source_branch_id = auth()->user()->branch_id;
        $withdrawal->save();

        foreach ($request->item_id as $index => $item_id) {
            Cardex::create([
                'source_branch_id' => auth()->user()->branch_id,
                'withdrawal_id' => $withdrawal->id,
                'item_id' => $item_id,
                'qty_out' => $request->request_qty[$index],
                'status' => 'RESERVED',
                'price_level_id' => $request->cost_price[$index],
                'tranasction_date' => $request->usage_date,
            ]);
        }

        return redirect()->route('withdrawal.index');
    }
    catch (\Exception $e) {
        return ($e->getMessage());
    }
}
}
