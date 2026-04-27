<?php

namespace App\Livewire\Validations;

use Livewire\Component;
use App\Models\BanquetProcurement;
use App\Models\BanquetEvent;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\Employee;
use Carbon\Carbon;

class BudgetProposalShow extends Component
{
    public $events = [];
    public $withdrawals = [];
    public $withdrawalInfo;
    public $withdrawnItems = [];
    public $selectedEvent;
    public $totalPercentage = 0;
    public $totalGrossOrder = 0;



    // selected equipment
    public $selectedEquipments = [];
    public $purchaseOrders = [];
    public $purchaseOrdersDetails = [];
    public $equipmentQty = [];
    public $inchargedBy = null;
    public $approver = null;
    public $handlingTeam = [];
    public $selectedEquipment = [];
     public $attachments = [];
    public $myNote = null;
    public $requestDocumentNumber = null;
    public $requestReferenceNumber = null;
    public $departmentName = null;
    public $approvers = [];
    public $reviewers = [];


    //forms
    public $isNewRequest = true; // Flag to check if it's a new request
    public $selectedApprover = null;
    public $selectedReviewer = null;
    public $selectedEventId = null; // To store the selected event ID
    public $documentNumber = null;
    public $requestedBudget = null;
    public $notes = null;
    public $saveAs= 'PREPARING';
    public $proposedBudgetId = null; // To store the proposal ID for updates
        public $hasServices = false;

    protected $rules = [
        'notes' => 'nullable|string|max:1000',
        'saveAs' => 'required|in:PREPARING,APPROVED,REJECTED,PENDING',
    ];
    public function render()
    {
        return view('livewire.validations.budget-proposal-show');
    }

    public function mount(Request $request)
    {
        // Check if a proposal ID is passed in the request
        if ($request->has('proposal-id')) {
            $this->isNewRequest = false; // Set to false since we are editing an existing proposal
            $proposalId = $request->input('proposal-id');
            $this->proposedBudgetId = $proposalId; // Store the proposal ID for updates
            $proposal = BanquetProcurement::with('notedBy', 'approver')->where('id', $proposalId)->where('status', 'PENDING')->first();
            if ($proposal) {
                $this->selectedEventId = $proposal->event_id;
                $this->documentNumber = $proposal->document_number;
                $this->selectedApprover = $proposal->approver->name . ' ' . $proposal->approver->last_name;
                $this->selectedReviewer = $proposal->notedBy->name . ' '. $proposal->notedBy->last_name;
                $this->requestedBudget = $proposal->suggested_amount;
                $this->approvedBudget = $proposal->approved_amount;
                $this->notes = $proposal->notes;
                $this->saveAs = $proposal->status;
                 $this->hasServices = $proposal->services_included ? true : false;
                $this->requestReferenceNumber = $proposal->reference_number;
                $this->loadEventDetails($this->selectedEventId);
                $this->updatedGrossOrder(); // Update the total percentage based on the loaded proposal data
            } else {
                session()->flash('error', 'Invalid Action.');
                return redirect()->to('/dashboard');
            }
        } else {
           session()->flash('error', 'Invalid Action.');
            return redirect()->to('/dashboard');
        }

    }

     public function loadEventDetails($eventId)
    {
        $this->selectedEvent = BanquetEvent::with('purchaseOrders','customer', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel')->find($eventId);
        $this->selectedEventId = $this->selectedEvent->id;
        if ($this->selectedEvent) {
            $this->dispatch('closeSelectEventModal');
            return $this->selectedEvent;
        } else {
            $this->dispatch('closeSelectEventModal');
            session()->flash('error', 'Invalid event selected.');
            return null;
        }
    }


    public function updateRequest()
    {

        $this->validate([
            'notes' => 'nullable|string|max:1000',
            'saveAs' => 'required|in:APPROVED,REJECTED,PREPARING',
        ]);


        $proposal = BanquetProcurement::find($this->proposedBudgetId);
        if ($proposal) {
            $proposal->update([
                'notes' => $this->notes,
                'status' => $this->saveAs,
                'updated_at' => \Carbon\Carbon::now('Asia/Manila'),
                'updated_by' => auth()->user()->emp_id,
            ]);
            $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Budget proposal updated successfully!']);
             $this->dispatch('viewTop');
        } else {
            session()->flash('error', 'Budget proposal not found.');
        }
    }

      public function updatedGrossOrder()
    {
        if($this->hasServices && $this->selectedEvent){
            $total = 0;
             $total +=
             isset($this->selectedEvent) && $this->selectedEvent->eventMenus ?
             $this->selectedEvent->eventMenus->sum(function($menu) {
                    return $menu->price->amount * ($menu->qty ? $menu->qty : 1); }): 0;
                $total += isset($this->selectedEvent) && $this->selectedEvent->eventServices ?
                $this->selectedEvent->eventServices->sum(function($service) {
                    return $service->price->amount * ($service->qty ? $service->qty : 1); }) : 0;
            $this->totalGrossOrder = $total;
            $this->updatedTotalPercentage();
        }else if(!$this->hasServices && $this->selectedEvent){
             $this->totalGrossOrder =  isset($this->selectedEvent) && $this->selectedEvent->eventMenus ?
             $this->selectedEvent->eventMenus->sum(function($menu) {
                    return $menu->price->amount * ($menu->qty ? $menu->qty : 1); }): 0;
                     $this->updatedTotalPercentage();
        }else{
             $this->totalGrossOrder = 0;
        }
    }

    public function updatedTotalPercentage()
    {
        if(!$this->selectedEvent){
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'Please select an event first!']);
            return;
        }
        if($this->selectedEvent){
            if($this->totalGrossOrder > ($this->requestedBudget)){
            $this->totalPercentage = ( $this->requestedBudget / $this->totalGrossOrder) * 100;

            }else{
                $this->totalPercentage = ( $this->requestedBudget / $this->requestedBudget) * 100;
            }

        }else{
                $this->requestedBudget = null;
            return;
        }
    }
}
