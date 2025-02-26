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

class PurchaseOrderController extends Controller
{
    //

    public function po_summary()
    {   
       // dd($request->id);
       $requistions = requisitionInfos::with('supplier','preparer', 'approver')
                        ->where('CATEGORY', 'PO')
                        ->where('requisition_status', '!=', 'CANCELLED')
                        ->where('from_branch_id', Auth::user()->branch_id)
                        ->get();
    
       return view('purchase_order.po_summary', compact('requistions'));
     }


     public function show($id)
     {   
         $requestInfo = requisitionInfos::with('supplier','preparer','reviewer', 'approver','requisitionTypes','requisitionDetails')->where( 'id',  $id)->first();
         //return $requestInfo;
         return view('purchase_order.po_view', compact('requestInfo'));
     }

     public function GetRequisitionType(){
        $requistionType = requisitionTypes::all();
        return requisitionTypes::all();
     }
 public function newpo(){
    $suppliers = supplier::where('supp_status', 'ACTIVE')->get();
    $types =  requisitionTypes::all();
    $items = items::with('priceLevel','statuses')->get();
    $approver = signatories::where('signatory_type', 'APPROVER')->get();
    $reviewer = signatories::where('signatory_type', 'REVIEWER')->get();
     return view('purchase_order.po_create', compact('suppliers','types','items','approver','reviewer'));
    // return statuses::all();
 }

     public function printPO($id)
     {       
         $requestInfo = requisitionInfos::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','requisitionTypes')->where('id',  $id )->first();
       // return $requestInfo;
         return view('purchase_order.po_print_details', compact('requestInfo'));
     }

     public function approval_request_list(){
        $approval_requests = requisitionInfos::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','requisitionTypes')->where([
            ['requisition_status', 'FOR APPROVAL'],
            ['category', 'PO'],['approved_by', Auth::user()->emp_id]])->get();
        $rejected_requests = requisitionInfos::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','requisitionTypes')
        ->where(function($query) {
            $query->whereNotNull('REJECTED_DATE')
              ->orWhereNotNull('APPROVED_DATE');
        })
        ->where([['APPROVED_BY', Auth::user()->emp_id],['category', 'PO']])
        ->get();

        return view('purchase_order.po_approval_request_list', compact('approval_requests','rejected_requests'));
     }


     public function review_request_list(){
        $review_requests = requisitionInfos::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','requisitionTypes')->where([
            ['requisition_status', 'FOR REVIEW'],
            ['category', 'PO'],
            ['REVIEWED_BY', Auth::user()->emp_id]])->get();
        $all_review_requests = requisitionInfos::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','requisitionTypes')
            ->whereNotNull('REVIEWED_DATE')
            ->where([['REVIEWED_BY', Auth::user()->emp_id],['category', 'PO']])
            ->get();
    
        return view('purchase_order.po_review_request', compact('review_requests','all_review_requests'));
     }

    public function po_edit($id=null)
    {
        $requisitionInfo = requisitionInfos::with('requisitionDetails')->where([['category', 'PO'],['requisition_status', 'preparing']])->find($id);
        $suppliers = supplier::where('supp_status', 'ACTIVE')->get();
        $types =  requisitionTypes::all();
        $items = items::with('priceLevel','statuses')->get();
        $approver = signatories::where('signatory_type', 'APPROVER')->get();
        $reviewer = signatories::where('signatory_type', 'REVIEWER')->get();
        return view('purchase_order.po_update', compact('requisitionInfo', 'suppliers', 'types', 'items', 'approver', 'reviewer'));
    }

    public function po_update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Save to requisitionInfos table
                $requisitionInfo = requisitionInfos::find($request->id);
                $requisitionInfo->supplier_id = $request->supp_id;
                $requisitionInfo->prepared_by = Auth::user()->emp_id;
                $requisitionInfo->approved_by = $request->approver_id;
                $requisitionInfo->reviewed_by = $request->reviewer_id;
                $requisitionInfo->requisition_status = 'PREPARING';
                $requisitionInfo->trans_date = now();
                $requisitionInfo->requisition_types_id = $request->type_id;
                $requisitionInfo->remarks = $request->remarks;
                $requisitionInfo->requisition_types_id = $request->type_id;
                $requisitionInfo->category = 'PO';
                $requisitionInfo->merchandise_po_number = $request->merchandise_po_number;
                $requisitionInfo->save();

                // Ensure item_id and request_qty are arrays
                $itemIds = $request->input('item_id', []);
                $requestQtys = $request->input('request_qty', []);

                // Check if both arrays are not empty and have the same length
                if (empty($itemIds) || empty($requestQtys) || count($itemIds) !== count($requestQtys)) {
                    throw new \Exception('Item IDs and Request Quantities must have the same length and cannot be empty.');
                }

                // Update requisitionDetails table
                $existingDetails = requisitionDetails::where('requisition_info_id', $requisitionInfo->id)->get();
                foreach ($existingDetails as $detail) {
                    $index = array_search($detail->item_id, $itemIds);
                    if ($index !== false) {
                        $detail->qty = $requestQtys[$index];
                        $detail->save();
                        unset($itemIds[$index]);
                        unset($requestQtys[$index]);
                    } else {
                        $detail->delete();
                    }
                }

                // Add new requisitionDetails
                foreach ($requestQtys as $index => $qty) {
                    $requisitionDetail = new requisitionDetails();
                    $requisitionDetail->requisition_info_id = $requisitionInfo->id;
                    $requisitionDetail->item_id = $itemIds[$index];
                    $requisitionDetail->qty = $qty;
                    $requisitionDetail->save();
                }
            });

            // Set success status
            return redirect()->route('purchase_order.po_summary')->with('status', 'success');
        } catch (\Exception $e) {
            // Set error status
            dd($e);
            return redirect()->route('purchase_order.po_summary')->with('status', 'error');
        }
    }

    public function storePO(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Generate a unique requisition number
                $requisitionNumber = 'PO-' . strtoupper(uniqid());
                $branchId = Auth::user()->branch_id;

                // Save to requisitionInfos table
                $requisitionInfo = new requisitionInfos();
                $requisitionInfo->supplier_id = $request->supp_id;
                $requisitionInfo->prepared_by = Auth::user()->emp_id;
                $requisitionInfo->approved_by = $request->approver_id;
                $requisitionInfo->reviewed_by = $request->reviewer_id;
                $requisitionInfo->requisition_status = 'PREPARING';
                $requisitionInfo->trans_date = now();
                $requisitionInfo->requisition_types_id = $request->type_id;
                $requisitionInfo->remarks = $request->remarks;
                $requisitionInfo->requisition_types_id = $request->type_id;
                $requisitionInfo->category = 'PO';
                $requisitionInfo->merchandise_po_number = $request->merchandise_po_number;
                $requisitionInfo->requisition_number = $requisitionNumber;
                $requisitionInfo->from_branch_id = $branchId;
                $requisitionInfo->save();
               
                // Ensure item_id and request_qty are arrays
                $itemIds = $request->input('item_id', []);
                $requestQtys = $request->input('request_qty', []);

                // Save to requisitionDetails table
                foreach ($requestQtys as $index => $qty) {
                    $requisitionDetail = new requisitionDetails();
                    $requisitionDetail->requisition_info_id = $requisitionInfo->id;
                    $requisitionDetail->item_id = $itemIds[$index];
                    $requisitionDetail->qty = $qty;
                    $requisitionDetail->save();
                }
            });

            // Set success status
            return redirect()->route('purchase_order.po_summary')->with('status', 'success');
        } catch (\Exception $e) {
            // Set error status
            dd($e);
            return redirect()->route('purchase_order.po_summary')->with('status', 'error');
        }
    }

    public function approvePO(Request $request)
    {
        try {
            dd($request);
            DB::transaction(function () use ($request) {
                $requisitionInfo = requisitionInfos::find($request->id);
                $requisitionInfo->requisition_status = 'PENDING';
                $requisitionInfo->approval_status = 'APPROVED';
                $requisitionInfo->save();
            });

            // Set success status
            return redirect()->route('purchase_order.po_approval_request_list')->with('status', 'success');
        } catch (\Exception $e) {
            // Set error status
            return redirect()->route('purchase_order.po_approval_request_list')->with('status', 'error');
        }
    }

    public function rejectPO(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $requisitionInfo = requisitionInfos::find($request->id);
                $requisitionInfo->requisition_status = 'REJECTED';
                $requisitionInfo->approval_status = 'REJECTED';
                $requisitionInfo->save();
            });

            // Set success status
            return redirect()->route('purchase_order.po_approval_request_list')->with('status', 'success');
        } catch (\Exception $e) {
            // Set error status
            return redirect()->route('purchase_order.po_approval_request_list')->with('status', 'error');
        }
    }

    public function poReviewed(Request $request, $id)
    {
        try {
            $requisition = requisitionInfos::find($id);
            if ($requisition) {
                $requisition->requisition_status = $request->status;
                if ($request->status === 'PENDING') {
                    $requisition->approved_date = now();
                }elseif ($request->status === 'REJECTED') {
                    $requisition->rejected_date = now();
                }else{
                    $requisition->reviewed_date = now();
                }
                
                $requisition->save();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Requisition not found']);
        } catch (\Exception $e) {
            \Log::error('Error updating requisition status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal Server Error']);
            dd($e);
        }
    }

    public function poRejected(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $requisitionInfo = requisitionInfos::find($request->id);
                $requisitionInfo->requisition_status = 'REJECTED';
                $requisitionInfo->save();
            });

            // Set success status
            return redirect()->route('purchase_order.po_review_request')->with('status', 'success');
        } catch (\Exception $e) {
            // Set error status
            return redirect()->route('purchase_order.po_review_request')->with('status', 'error');
        }
    }

    public function poCancelled(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $requisitionInfo = requisitionInfos::find($request->id);
                $requisitionInfo->requisition_status = 'CANCELLED';
                $requisitionInfo->save();
            });

            // Set success status
            return redirect()->route('purchase_order.po_summary')->with('status', 'success');
        } catch (\Exception $e) {
            // Set error status
            return redirect()->route('purchase_order.po_summary')->with('status', 'error');
        }
    }


    public function po_printed(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $requisitionInfo = requisitionInfos::find($request->id);
                $requisitionInfo->requisition_status = 'FOR REVIEW';
                $requisitionInfo->save();
            });

            // Set success status
            return redirect()->route('purchase_order.po_summary')->with('response', 'okay kaayo');
        } catch (\Exception $e) {
            // Set error status
            return redirect()->route('purchase_order.po_summary')->with('status', 'error');
        }
    }


    public function show_review_request($id)
    {   
        $requestInfo = requisitionInfos::with('supplier','preparer','reviewer', 'approver','requisitionTypes','requisitionDetails')->where( 'id',  $id)->first();
        //return $requestInfo;
        return view('purchase_order.po_show_for_review', compact('requestInfo'));
    }
    public function show_approval_request($id)
    {   
        $requestInfo = requisitionInfos::with('supplier','preparer','reviewer', 'approver','requisitionTypes','requisitionDetails')->where( 'id',  $id)->first();
        //return $requestInfo;
        return view('purchase_order.po_show_for_approval', compact('requestInfo'));
    }

    public function getPODetailsNOneSense($poNumber)
    {
        try {
            $requisitionInfo = requisitionInfos::with('supplier', 'preparer', 'reviewer', 'approver', 'requisitionTypes', 'requisitionDetails.items.priceLevel')
                ->where('requisition_number', $poNumber)
                ->first();

            if ($requisitionInfo) {
                return response()->json(['success' => true, 'requisitionInfo' => $requisitionInfo]);
            } else {
                return response()->json(['success' => false, 'message' => 'PO not found']);
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching PO details: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal Server Error']);
        }
    }

}

