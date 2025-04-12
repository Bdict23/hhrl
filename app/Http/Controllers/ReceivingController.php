<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Term;
use App\Models\Item;
use App\Models\PriceLevel;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Signatory;
use App\Models\Receiving;
use App\Models\Cardex;
use Exception;
use App\Traits\HasCostCalculation;
class ReceivingController extends Controller
{
    use HasCostCalculation;
    public function getPODetails($request = null) {
        // dd($request);
        $checkers = Signatory::where('signatory_type', 'checker')->get();
        $allocators = Signatory::where('signatory_type', 'allocator')->get();
        $requestInfos = RequisitionInfo::with([
            'supplier',
            'preparer',
            'reviewer',
            'approver',
            'requisitionTypes',
            'requisitionDetails.item.priceLevels' // Eager load price levels with items
        ])
        ->where('FROM_BRANCH_ID', Auth::user()->branch_id)
        ->whereIn('requisition_status', ['TO RECEIVE', 'PARTIALLY FULLFILLED'])
        ->where('requisition_number', $request)
        ->first();

        

        $cardexSum = Cardex::select('item_id', DB::raw('SUM(qty_in) as total_received'))
        ->where('transaction_type', 'RECEVING')
        ->where('requisition_id', $requestInfos->id ?? 0)
        ->groupBy('item_id')
        ->get() ?? [];
        $cardexSum = collect($cardexSum)->keyBy('item_id')->all();
            
        try {
            if (!$requestInfos) {
                throw new \Exception("RequestInfo not found or null.");
            }
        
            if (!$requestInfos->requisitionDetails) {
                throw new \Exception("Requisition details are missing.");
            }
        
            $requestInfos->requisitionDetails->each(function ($detail) {
                if ($detail->item) { // ✅ Check if item exists
                    $detail->item->current_cost = $this->calculateItemCost($detail->item); // ✅ Calculate the current cost
                } else {
                    $detail->item = (object) ['current_cost' => 0]; // ✅ Prevent null errors
                }
            });
        } catch (\Exception $e) {
            \Log::error("Error processing requisition details: " . $e->getMessage());
        }
         return view('purchase_order.po_receive', compact('requestInfos', 'checkers', 'allocators','cardexSum'));

     }
     
    public function po_store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'receive_from' => 'required|string',
                'po_number' => 'required|string',
                'merchandise_po_number' => 'nullable|string',
                'date' => 'required|date',
                'way_bill_no' => 'nullable|string',
                'delivery_no' => 'nullable|string',
                'invoice_no' => 'nullable|string',
                'receiving_packing_no' => 'required|string',
                'receiving_date' => 'required|date',
                'remarks' => 'nullable|string',
                'stf_id' => 'nullable|integer',
                'checked_by' => 'required|integer',
                'allocated_by' => 'required|integer',
                'received_qty' => 'required|array',
                'received_qty.*' => 'required|integer|min:0',
                'item_id' => 'required|array',
                'item_id.*' => 'required|integer',
                'requisition_id' => 'required|array',
                'requisition_id.*' => 'required|integer',
            ]);

            // Save the receiving data
            $receiving = new Receiving();
            $receiving->requisition_id = $request->po_id;
            $receiving->packing_number = $request->receiving_packing_no;
            $receiving->receiving_type = 'PO';
            $receiving->delivery_number = $request->delivery_no;
            $receiving->invoice_number = $request->invoice_no;
            $receiving->WAYBILL_NUMBER = $request->way_bill_no;
            $receiving->received_date = $request->date;
            $receiving->remarks = $request->remarks;
            $receiving->checked_by = $request->checked_by;
            $receiving->allocated_by = $request->allocated_by;
            $receiving->transaction_date = $request->receiving_date;
            $receiving->delivered_by = $request->receive_from;
            $receiving->save();

            $bo_count = 0;
            // Save the received quantities to the cardex table
            foreach ($request->received_qty as $index => $qty) {
                // Get the requisition quantity
                $requisitionDetail = RequisitionDetail::where('item_id', $request->item_id[$index])
                    ->where('requisition_info_id', $request->requisition_id[$index])
                    ->first();
                if ($requisitionDetail && $qty > $requisitionDetail->qty) {
                    throw new Exception('Received quantity cannot be greater than requested quantity for item ID: ' . $request->item_id[$index]);
                } else if ($requisitionDetail && $qty < $requisitionDetail->qty) {
                    $this->createBackOrder($request->requisition_id[$index], $request->item_id[$index], $qty);
                }
                $cardex = new Cardex();
                $cardex->source_branch_id = Auth::user()->branch_id;
                $cardex->qty_in = $qty;
                $cardex->qty_out = 0;
                $cardex->item_id = $request->item_id[$index];
                $cardex->status = 'TEMP';
                $cardex->transaction_type = 'RECEVING';
                $cardex->receiving_id = $receiving->id;
                $cardex->requisition_id = $request->requisition_id[$index];
                $cardex->save();
            }

            // Redirect with success message
            return redirect()->route('po.receive_stock')->with('status', 'success');
        } catch (Exception $e) {
            // Log the error message
            \Log::error('Error saving receiving data: ' . $e->getMessage());

            // Redirect with error message

            return redirect()->route('po.receive_stock')->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    private function createBackOrder($requisitionId, $itemId, $backOrderQty)
    {
        $total_rec = DB::table('cardex')
            ->where('requisition_id', $requisitionId)
            ->where('item_id', $itemId)
            ->where('transaction_type', 'RECEVING')
            ->sum('qty_in') ?? 0;

        $total_req_qty = RequisitionDetail::where('requisition_info_id', $requisitionId)
            ->where('item_id', $itemId)
            ->sum('qty') ?? 0;

            $existingBackOrder = DB::table('backorders')
                ->where('requisition_id', $requisitionId)
                ->where('item_id', $itemId)
                ->first();

            if ($existingBackOrder) {

                if ($total_rec + $backOrderQty == $total_req_qty) {
                    DB::table('backorders')
                        ->where('requisition_id', $requisitionId)
                        ->where('item_id', $itemId)
                        ->update(['status' => 'FULLFILLED']);
                }

            } else {

                    DB::table('backorders')->insert([
                        'requisition_id' => $requisitionId,
                        'item_id' => $itemId,
                        'STATUS' => 'ACTIVE',
                        'bo_type' => 'PO',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

            }
    }
}
