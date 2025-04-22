<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Term;
use App\Models\Item;
use App\Models\PriceLevel;
use App\Models\Signatory;
use App\Models\Cardex;
use App\Models\Department;
use App\Models\Withdrawal;

use Illuminate\Support\Facades\DB;


class InventoryAdjustmentController extends Controller
{

    //withdrawal summary route
    public function withdrawalSummary(){
        $withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'preparedBy', 'cardex')->where('source_branch_id', auth()->user()->branch_id)->get();
        return view('inventory.withdrawal_summary', compact('withdrawals'));
    }



    public function storeWithdrawal(Request $request){
        // dd($request->all());
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
        $withdrawal->remarks = $request->remarks;
        $withdrawal->withdrawal_status = ($request->finalStatus == 'YES' ? 'FOR REVIEW' : 'PREPARING');
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
                'transaction_type' => 'WITHDRAWAL'
            ]);
        }

        return redirect()->route('withdrawal.index');
    }
    catch (\Exception $e) {
        return ($e->getMessage());
    }
}
public function withdrawalReview() {

    $withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->where('reviewed_by', auth()->user()->emp_id)->get();
    return view('inventory.withdrawal_review_lists', compact('withdrawals'));
}
public function editWithdrawal($id) {
    $withdrawal = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->findOrFail($id);
    $departments = Department::where([['company_id', auth()->user()->branch->company_id], ['department_status', 'ACTIVE'], ['branch_id', auth()->user()->branch_id]])->get();
    $approvers = Signatory::where([['signatory_type', 'APPROVER', 'employees'], ['branch_id', auth()->user()->branch_id], ['status', 'ACTIVE'], ['MODULE', 'ITEM_WITHDRAWAL']])->get();
    $reviewers = Signatory::where([['signatory_type', 'REVIEWER', 'employees'], ['branch_id', auth()->user()->branch_id], ['status', 'ACTIVE'], ['MODULE', 'ITEM_WITHDRAWAL']])->get();
    $items = Item::with('priceLevel', 'units', 'category', 'classification')->where([['item_status', 'ACTIVE'], ['company_id', auth()->user()->branch->company_id]])->get();

    return view('inventory.edit_withdrawal', compact('withdrawal', 'departments', 'approvers', 'reviewers', 'items'));
}

public function withdrawalApproval() {
    $withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->where('approved_by', auth()->user()->emp_id)->get();
    return view('inventory.withdrawal_approval_lists', compact('withdrawals'));
}


public function printWidthrawal($id) {
    try {
        Cardex::where('withdrawal_id', $id)->update(['status' => 'FINAL']);
        return redirect()->route('withdrawal.index')->with('success', 'Withdrawal status updated and finalized successfully.');

    } catch (\Exception $e) {
        return ($e->getMessage());
    }
}

}
