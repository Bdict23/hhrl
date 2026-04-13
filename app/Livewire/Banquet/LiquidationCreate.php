<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\EventLiquidation;
use WireUi\Traits\WireUiActions;
use App\Models\BanquetEvent;
use Carbon\Carbon;
use App\Models\AcknowledgementReceipt;
use App\Models\RequisitionInfo;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\CashReturn;
use App\Models\PettyCashVoucher;
use Illuminate\Http\Request;




class LiquidationCreate extends Component
{
    use WireUiActions;

    // mounted
    public $createDate ;
    public $events;
    public $reviewers;
    public $approvers;
    public $crsApprover;
    public $hasCRS = false;
    public $isLiquidationExists = false;
    public $isEditable = true;
    public $isApproval=false;
    public $isValidator=false;
    public $status;

    // selected 
    public $selectedEventId;
    public $checkDetails;
    public $checkNumber;
    public $checkAmount;
    public $purchaseOrders;
    public $pettyCashVouchers;
    public $incurredAmount;
    public $totalExpense;
    public $remarks;
    public $selectedApproverId;
    public $selectedReviewerId;
    public $crsReference;
    public $amountReturned;
    public $liquidationNotes;
    public $saveAs = 'DRAFT';
    public $referenceNumber;
    public $totalCashReturnFromPCV = 0;
    
    //CRS fillable
    public $selectedCrsApproverId;
    public $selectedEventIdforCrs;
    public $returnAmount;
    public $eventName;
    public $crsNote;

    // fillable


    public function render()
    {
        return view('livewire.banquet.liquidation-create');
    }
    public function mount(Request $request = null){
          if($request && $request->has('BEO-LIQ-id')){
            $liquidationId = $request->query('BEO-LIQ-id');
            $liquidationData = EventLiquidation::find($liquidationId);
            if($liquidationData){
                $this->getExistingLiquidationData($liquidationData);
            }else{
                $this->notify('Invalid ID', 'error', 'The provided BEO Liquidation ID is invalid.');
            }
          }else if($request && $request->has('BEO-Approval-id')){
            $liquidationId = $request->query('BEO-Approval-id');
            $liquidationData = EventLiquidation::find($liquidationId);
            if($liquidationData){
                $this->isApproval = true;
                $this->saveAs = 'APPROVED';
                $this->getExistingLiquidationData($liquidationData);
            }else{
                $this->notify('Invalid ID', 'error', 'The provided BEO Liquidation ID is invalid.');
            }
          }else if($request && $request->has('BEO-Validate-id')){
            $liquidationId = $request->query('BEO-Validate-id');
            $liquidationData = EventLiquidation::find($liquidationId);
            if($liquidationData){
                $this->isValidator = true;
                $this->getExistingLiquidationData($liquidationData);
            }else{
                $this->notify('Invalid ID', 'error', 'The provided BEO Liquidation ID is invalid.');
            }
          }else{
            $this->createDate =  Carbon::now()->format('d-m-Y H:i');
            $this->events = BanquetEvent::where('branch_id', auth()->user()->branch_id)
            ->whereIn('status', ['CONFIRMED', 'CLOSED'])
            ->whereDoesntHave('liquidation') // Exclude events that already have a liquidation
            ->get();
          }
            $this->fetchData();

    }
    public function fetchData(){
        
            // 1. Get the ID directly (consider caching this if it rarely changes)
            $moduleId = Module::where('module_name', 'BEO Liquidation')->value('id');
            $crsModuleId = Module::where('module_name', 'BEO Cash Return')->value('id');


            // 2. Fetch both types in a single query
            $allSignatories = Signatory::with('employees') // Eager load only necessary columns
                ->where('module_id', $moduleId)
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('signatory_type', ['APPROVER', 'REVIEWER'])
                ->get()->map(function ($user) {
                return [
                    'id' => $user->employees->id,
                    'full_name' => $user->employees->name . ' ' . $user->employees->middle_name. ' ' . $user->employees->last_name,
                    'signatory_type' => $user->signatory_type,
                ];
            });
               $allSignatoriesCRS = Signatory::with('employees') // Eager load only necessary columns
                ->where('module_id', $crsModuleId)
                ->where('branch_id', auth()->user()->branch_id)
                ->whereIn('signatory_type', ['APPROVER', 'REVIEWER'])
                ->get()->map(function ($user) {
                return [
                    'id' => $user->employees->id,
                    'full_name' => $user->employees->name . ' ' . $user->employees->middle_name. ' ' . $user->employees->last_name,
                    'signatory_type' => $user->signatory_type,
                ];
            });

            // 3. Partition the collection in memory (No extra DB hits)
            $this->approvers = $allSignatories->where('signatory_type', 'APPROVER');
            $this->reviewers = $allSignatories->where('signatory_type', 'REVIEWER');
            $this->crsApprover = $allSignatoriesCRS->where('signatory_type', 'APPROVER');

    }
    public function getExistingLiquidationData($liquidationData)
    {
        $this->isLiquidationExists = true;
        $this->isEditable = $liquidationData->status === 'DRAFT';
        $this->status = $liquidationData->status;
        $this->events = BanquetEvent::where('id', $liquidationData->event_id)->get();
        $this->referenceNumber = $liquidationData->reference;
        $this->createDate = Carbon::parse($liquidationData->created_at)->format('d-m-Y H:i');
        $this->liquidationNotes = $liquidationData->purpose;
        $this->incurredAmount = $liquidationData->total_incurred;
        $this->selectedApproverId = $liquidationData->approved_by;
        $this->selectedReviewerId = $liquidationData->reviewed_by;
        $this->saveAs = $liquidationData->status;
        $this->selectedEventId = $liquidationData->event_id;
        $this->getEventInformation($this->events);
        
    }
    public function getEventInformation($event){
        $event = $event->first();
        $this->checkDetails = $event->acknowledgementReceipts()->whereIn('status', ['OPEN', 'CLOSED'])->first();
        // check
        if(!$this->checkDetails){
            $this->notify('No check number found', 'error', 'please process acknowledgement first.');
            return;
        }
        $this->checkNumber = $this->checkDetails->check_number;
        $this->checkAmount = $this->checkDetails->check_amount;

        // petty cash vouchers
        $this->pettyCashVouchers = $event->pettyCashVouchers()->with('cashReturn')->get();
        
        // PO
        $this->purchaseOrders = $event->purchaseOrders()->with('receivings')->get();

        foreach($this->pettyCashVouchers ?? [] as $pcv){
            if($pcv->cashReturn){
                $this->totalCashReturnFromPCV += $pcv->cashReturn->amount_returned;
            }
        }

         $this->totalExpense = $this->pettyCashVouchers->sum('total_amount') - $this->totalCashReturnFromPCV; // total expense + total of petty cash vouchers - total of cash return from petty cash vouchers
        if(!$this->isLiquidationExists){
           $this->incurredAmount = $this->totalExpense;
        }
        // CRS
        $checkCRS = $event->cashReturn()->first();
        $this->hasCRS = (bool) $checkCRS;
        $this->crsReference = $checkCRS->reference ?? null;
        $this->amountReturned = $checkCRS->amount_returned ?? null;
        
        
        $this->remarks = $this->checkAmount - $this->totalExpense;
        $this->returnAmount =  $this->remarks;
        
    }
    public function updatedSelectedEventId($value)
    {
        $this->selectedEventId = $value;
        $this->selectedEventIdforCrs = $value;
        
        $event = BanquetEvent::where('id', $value)->get();
       if($event && $value){
            $this->eventName = $event->first()->event_name ?? null;
            $this->getEventInformation($event);
       }else{
          $this->resetLiquidationForm();
       }
        
    }
    public function saveLiquidation()
    {
        $this->validate([
            'selectedEventId' => 'required|exists:banquet_events,id',
            'liquidationNotes' => 'nullable|string|max:255',
            'selectedApproverId' => 'required|exists:employees,id',
            'selectedReviewerId' => 'required|exists:employees,id',
            'incurredAmount' => 'required|numeric|min:0',
            'saveAs' => 'required|in:DRAFT,OPEN',
        ]);
        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = EventLiquidation::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'LQB-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);

        $liquidation = new EventLiquidation();
        $liquidation->reference = $reference;
        $liquidation->branch_id = $branchId;
        $liquidation->event_id = $this->selectedEventId;
        $liquidation->status = $this->saveAs;
        $liquidation->created_by = auth()->user()->emp_id;
        $liquidation->approved_by = $this->selectedApproverId;
        $liquidation->reviewed_by = $this->selectedReviewerId;
        $liquidation->purpose = $this->liquidationNotes;
        $liquidation->total_incurred = $this->incurredAmount;
        $liquidation->save();
        $this->isLiquidationExists = true;
        $this->isEditable = $this->saveAs === 'DRAFT';
        $this->notify('Successfuly saved!', 'success', 'BEO Liquidation successfully saved!');
        $this->resetLiquidationForm(); 
    }
    public function updateLiquidation()
    {
        $this->validate([
            'selectedEventId' => 'required|exists:banquet_events,id',
            'liquidationNotes' => 'nullable|string|max:255',
            'selectedApproverId' => 'required|exists:employees,id',
            'selectedReviewerId' => 'required|exists:employees,id',
            'incurredAmount' => 'required|numeric|min:0',
            'saveAs' => 'required|in:DRAFT,OPEN',
        ]);

        $liquidation = EventLiquidation::where('event_id', $this->selectedEventId)->first();
        if ($liquidation) {
            $liquidation->status = $this->saveAs;
            $liquidation->approved_by = $this->selectedApproverId;
            $liquidation->reviewed_by = $this->selectedReviewerId;
            $liquidation->purpose = $this->liquidationNotes;
            $liquidation->total_incurred = $this->incurredAmount;
            $liquidation->save();
            $this->isEditable = $this->saveAs === 'DRAFT';
            $this->status = $this->saveAs;
            $this->notify('Successfuly updated!', 'success', 'BEO Liquidation successfully updated!');
        } else {
            $this->notify('Not Found', 'error', 'No existing liquidation found for the selected event.');
        }
    }
    public function approvalAction()
    {
        $this->validate([
            'selectedEventId' => 'required|exists:banquet_events,id',
            'saveAs' => 'required|in:APPROVED,REVISE',
        ]);

        $liquidation = EventLiquidation::where('event_id', $this->selectedEventId)->first();
        if ($liquidation) {
            $liquidation->approved_date = $this->saveAs === 'APPROVED' ? Carbon::now() : null;
            $liquidation->status = $this->saveAs === 'APPROVED' ? 'OPEN' : 'DRAFT';
            $liquidation->save();
            $this->notify('Successfuly updated!', 'success', 'BEO Liquidation status successfully updated!');
            $this->resetLiquidationForm();
            // redirect to list page after approval action
            return redirect()->route('beo.liquidation.approval.lists');
        } else {
            $this->notify('Not Found', 'error', 'No existing liquidation found for the selected event.');
        }
    }
    public function validationAction()
    {
        $this->validate([
            'selectedEventId' => 'required|exists:banquet_events,id',
            'saveAs' => 'required|in:VALIDATED,REVISE',
        ]);

        $liquidation = EventLiquidation::where('event_id', $this->selectedEventId)->first();
        if ($liquidation) {
            $liquidation->reviewed_date = $this->saveAs === 'VALIDATED' ? Carbon::now() : null;
            $liquidation->status = $this->saveAs === 'VALIDATED' ? 'CLOSED' : 'DRAFT';
            $liquidation->save();
            $this->notify('Successfuly updated!', 'success', 'BEO Liquidation status successfully updated!');

            // update event to close if validated
            if($this->saveAs === 'VALIDATED'){
                $event = BanquetEvent::find($this->selectedEventId);
                $event->liquidation_status = 'LIQUIDATED';
                $event->liquidation_date = Carbon::now();
                $event->save();
            }
            $this->resetLiquidationForm();
            // redirect to list page after validation action
            return redirect()->route('beo.liquidation.validate.lists');
        } else {
            $this->notify('Not Found', 'error', 'No existing liquidation found for the selected event.');
        }
    }
    private function resetLiquidationForm()
    {
       $this->selectedEventId = null;
       $this->checkDetails = null;
       $this->checkNumber = null;
       $this->checkAmount = null;
       $this->purchaseOrders = null;
       $this->incurredAmount = null;
       $this->remarks = null;
       $this->selectedApproverId = null;
       $this->selectedReviewerId = null;
       $this->crsReference = null;
       $this->amountReturned = null;
        $this->liquidationNotes = null;
        $this->saveAs = 'DRAFT';
        $this->referenceNumber = null;
        $this->isLiquidationExists = false;
        $this->isEditable = true;
         $this->hasCRS = false;
         $this->pettyCashVouchers = null;
    }
    private function resetCrsForm(){
        $this->selectedCrsApproverId = null;
        $this->selectedEventIdforCrs = null;
        $this->returnAmount = null;
        $this->eventName = null;
        $this->crsNote = null;
    }

    
    public function saveCrs(){

         $this->validate([
            'returnAmount' => 'required|numeric|min:1',
            'selectedCrsApproverId' => 'required|exists:employees,id',
            'selectedEventIdforCrs' => 'required|exists:banquet_events,id',
            'crsNote' => 'nullable|string|max:50',
        ]);

        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = CashReturn::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'BCR-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);

        $crsCreate = new CashReturn();
        $crsCreate->branch_id = $branchId;
        $crsCreate->reference =  $reference;
        $crsCreate->status =  'FINAL';
        $crsCreate->event_id =  $this->selectedEventIdforCrs;
        $crsCreate->prepared_by =  auth()->user()->emp_id;
        $crsCreate->amount_returned =  $this->returnAmount;
        $crsCreate->notes =  $this->crsNote;
        $crsCreate->created_at =  Carbon::now();
        $crsCreate->approved_by =  $this->selectedCrsApproverId;
        $crsCreate->save();
        $this->modal()->close('cardModal');

        $this->notify('Successfuly saved!', 'success', 'BEO Cash return successfully saved!');
        $this->crsReference = $reference;
        $this->hasCRS = true;
        $this->amountReturned = $this->returnAmount;

        $this->resetCrsForm();

    }
    public function createCRS(){
        if($this->hasCRS && $this->selectedEventId){
              $this->notify('Already Exists', 'error', 'Cash Return for Selected Event already Exists');
              return;
        }else if(!$this->selectedEventId){
              $this->notify('Select BEO first', 'error', 'Please select BEO before creating Cash Return.');
            return;
        }else{
            $this->modal()->open('cardModal');
        }
    }

    public function notify($title, $icon, $description) : void
    {
        $this->notification()->send([
            'icon' => $icon,
            'title' => $title,
            'description' => $description,
        ]);
    }

}

