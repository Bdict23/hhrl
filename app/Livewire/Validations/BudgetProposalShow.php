<?php

namespace App\Livewire\Validations;

use Livewire\Component;
use App\Models\BanquetProcurement;
use App\Models\BanquetEvent;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\Employee;    

class BudgetProposalShow extends Component
{
    public $events = [];
    public $withdrawals = [];
    public $withdrawalInfo;
    public $withdrawnItems = [];
    public $selectedEvent;



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
    public $approveBudget = null;
    public $isFinal = 'PREPARING';
    public $proposedBudgetId = null; // To store the proposal ID for updates

    protected $rules = [
        'notes' => 'nullable|string|max:1000',
        'approveBudget' => 'required|numeric|min:0',
        'isFinal' => 'required|in:PREPARING,APPROVED,REJECTED,PENDING',
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
            $proposal = BanquetProcurement::with('notedBy', 'approver')->where('id', $proposalId)->first();
            if ($proposal) {
                $this->selectedEventId = $proposal->event_id;
                $this->documentNumber = $proposal->document_number;
                $this->selectedApprover = $proposal->approver->name . ' ' . $proposal->approver->last_name;
                $this->selectedReviewer = $proposal->notedBy->name . ' '. $proposal->notedBy->last_name;
                $this->requestedBudget = $proposal->suggested_amount;
                $this->notes = $proposal->notes;
                $this->isFinal = $proposal->status;
                $this->requestReferenceNumber = $proposal->reference_number;
                $this->loadEventDetails($this->selectedEventId);
            } else {
                session()->flash('error', 'Invalid event selected.');
                return redirect()->to('/banquet-procurement-lists');
            }
        } else {
           session()->flash('error', 'Invalid event selected.');
            return redirect()->to('/banquet-procurement-lists');
        }
      
    }

     public function loadEventDetails($eventId)
    {
        $this->selectedEvent = BanquetEvent::with('purchaseOrders','customer', 'venue', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel')->find($eventId);
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
        if($this->isFinal != 'REJECTED'){
             $this->validate([
            'notes' => 'nullable|string|max:1000',
            'approveBudget' => 'required|numeric|min:0',
            'isFinal' => 'required|in:PREPARING,APPROVED,REJECTED,PENDING',
            ]);
        }else{
            $this->validate([
            'notes' => 'nullable|string|max:1000',
            'isFinal' => 'required|in:REJECTED',
        ]);
        }
       
        $proposal = BanquetProcurement::find($this->proposedBudgetId);
        if ($proposal) {
            $proposal->update([
                'notes' => $this->notes,
                'status' => $this->isFinal,
                'approved_amount' => $this->approveBudget,
            ]);
            session()->flash('success', 'Budget proposal updated successfully.');
        } else {
            session()->flash('error', 'Budget proposal not found.');
        }
    }
}
