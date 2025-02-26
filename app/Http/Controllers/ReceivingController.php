<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\requisitionInfos;
use App\Models\requisitionDetails;
use App\Models\supplier;
use App\Models\employees;
use App\Models\Branch;
use App\Models\requisitionTypes;
use App\Models\items;
use App\Models\priceLevel;
use App\Models\statuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\signatories;
use App\Models\receiving;
use App\Models\cardex;
use Exception;

class ReceivingController extends Controller
{
    public function getPODetails($request = null) {
        //dd($request);
       

       
        
        $checkers = signatories::where('signatory_type', 'checker')->get();
        $allocators = signatories::where('signatory_type', 'allocator')->get();
        $requestInfos = requisitionInfos::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','requisitionTypes')->where('requisition_number', $request)->first();
        $cardexSum = cardex::select('item_id', DB::raw('SUM(qty_in) as total_received'))
        ->where('transaction_type', 'RECEVING')
        ->where('requisition_id', $requestInfos->id ?? 0)
        ->groupBy('item_id')
        ->get() ?? [];
        $cardexSum = collect($cardexSum)->keyBy('item_id')->all();
         return view('purchase_order.po_receive', compact('requestInfos', 'checkers', 'allocators','cardexSum'));
        // return statuses::all();
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
            $receiving = new receiving();
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
                $requisitionDetail = requisitionDetails::where('item_id', $request->item_id[$index])
                    ->where('requisition_info_id', $request->requisition_id[$index])
                    ->first();
                if ($requisitionDetail && $qty > $requisitionDetail->qty) {
                    throw new Exception('Received quantity cannot be greater than requested quantity for item ID: ' . $request->item_id[$index]);
                } else if ($requisitionDetail && $qty < $requisitionDetail->qty) {
                    $this->createBackOrder($request->requisition_id[$index], $request->item_id[$index], $qty);
                }
                $cardex = new cardex();
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

        $total_req_qty = requisitionDetails::where('requisition_info_id', $requisitionId)
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
