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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Signatory;

class PurchaseOrderController extends Controller
{
    //

    public function po_summary()
    {
       // dd($request->id);
       $requistions = RequisitionInfo::with('supplier','preparer', 'approver')
                        ->where('CATEGORY', 'PO')
                        ->where('requisition_status', '!=', 'CANCELLED')
                        ->where('from_branch_id', Auth::user()->branch_id)
                        ->get();

       return view('purchase_order.po_summary', compact('requistions'));
     }


     public function show($id)
     {
         $requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
         //return $requestInfo;
         return view('purchase_order.po_view', compact('requestInfo'));
     }

     public function GetRequisitionType(){
        $requistionType = Term::all();
        return Term::all();
     }


     public function printPO($id)
     {
         $requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')->where('id',  $id )->first();
       // return $requestInfo;
         return view('purchase_order.po_print_details', compact('requestInfo'));
     }

     public function approval_request_list(){
        $approval_requests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')->where([
            ['requisition_status', 'FOR APPROVAL'],
            ['category', 'PO'],['approved_by', Auth::user()->emp_id]])->get();
        $rejected_requests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')
        ->where(function($query) {
            $query->whereNotNull('REJECTED_DATE')
              ->orWhereNotNull('APPROVED_DATE');
        })
        ->where([['APPROVED_BY', Auth::user()->emp_id],['category', 'PO']])
        ->get();

        return view('purchase_order.po_approval_request_list', compact('approval_requests','rejected_requests'));
     }


     public function review_request_list(){
        $review_requests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')->where([
            ['requisition_status', 'FOR REVIEW'],
            ['category', 'PO'],
            ['REVIEWED_BY', Auth::user()->emp_id]])->get();
        $all_review_requests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')
            ->whereNotNull('REVIEWED_DATE')
            ->where([['REVIEWED_BY', Auth::user()->emp_id],['category', 'PO']])
            ->get();

        return view('purchase_order.po_review_request', compact('review_requests','all_review_requests'));
     }

    public function po_edit($id=null)
    {
        $requisitionInfo = RequisitionInfo::with('requisitionDetails')->where([['category', 'PO'],['requisition_status', 'preparing']])->find($id);
        $suppliers = Supplier::where('supplier_status', 'ACTIVE')->get();
        $terms =  Term::all();
        $items = Item::with('priceLevel')->where('item_status', 'ACTIVE' )->get();
        $approver = Signatory::where('signatory_type', 'APPROVER')->get();
        $reviewer = Signatory::where('signatory_type', 'REVIEWER')->get();
        return view('purchase_order.po_update', compact('requisitionInfo', 'suppliers', 'terms', 'items', 'approver', 'reviewer'));
    }

    public function po_update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Save to requisitionInfos table
                $requisitionInfo = RequisitionInfo::find($request->id);
                $requisitionInfo->supplier_id = $request->supp_id;
                $requisitionInfo->prepared_by = Auth::user()->emp_id;
                $requisitionInfo->approved_by = $request->approver_id;
                $requisitionInfo->reviewed_by = $request->reviewer_id;
                $requisitionInfo->requisition_status = 'PREPARING';
                $requisitionInfo->trans_date = now();
                $requisitionInfo->term_id = $request->term_id;
                $requisitionInfo->remarks = $request->remarks;
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
                $existingDetails = RequisitionDetail::where('requisition_info_id', $requisitionInfo->id)->get();
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
                    $requisitionDetail = new RequisitionDetail();
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
                $requisitionInfo = new RequisitionInfo();
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
                    $requisitionDetail = new RequisitionDetail();
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
                $requisitionInfo = RequisitionInfo::find($request->id);
                $requisitionInfo->requisition_status = 'TO RECEIVE';
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
                $requisitionInfo = RequisitionInfo::find($request->id);
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
            $requisition = RequisitionInfo::find($id);
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
                $requisitionInfo = RequisitionInfo::find($request->id);
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
                $requisitionInfo = RequisitionInfo::find($request->id);
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
            $requsition = RequisitionInfo::find($request->id);
            if($requsition->requisition_status == 'PREPARING'){
                DB::transaction(function () use ($request) {
                    $requisitionInfo = RequisitionInfo::find($request->id);
                    $requisitionInfo->requisition_status = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Purchase Order') == 1 ? 'FOR REVIEW': 'FOR APPROVAL';
                    $requisitionInfo->save();
                });
            }
            

            // Set success status
            return redirect()->route('purchase_order.po_summary')->with('response', 'okay kaayo');
        } catch (\Exception $e) {
            // Set error status
            return redirect()->route('purchase_order.po_summary')->with('status', 'error');
        }
    }


    public function show_review_request($id)
    {

        $requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
        //return $requestInfo;
        return view('purchase_order.po_show_for_review', compact('requestInfo'));
    }
    public function show_approval_request($id)
    {
        $requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
        //return $requestInfo;
        return view('purchase_order.po_show_for_approval', compact('requestInfo'));
    }

    public function getPODetailsNOneSense($poNumber)
    {
        try {
            $requisitionInfo = RequisitionInfo::with('supplier', 'preparer', 'reviewer', 'approver', 'term', 'requisitionDetails.items.priceLevel')
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

