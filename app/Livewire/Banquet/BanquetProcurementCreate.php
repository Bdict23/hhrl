<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent;
use App\Models\Withdrawal;
use App\Models\Cardex;
use App\Models\EquipmentRequest;
use App\Models\EquipmentRequestAttachment;
use App\Models\Signatory;
use App\Models\Module;
use App\Models\BanquetProcurement;
use Illuminate\Http\Request;

class BanquetProcurementCreate extends Component
{
    public $banquetEventBudget;
    public $events = [];
    public $selectedEvent;
    public $totalPercentage = 0;
    public $totalGrossOrder = 0;
    public $bebId = null;



    // selected equipment
    public $inchargedBy = null;
    public $approver = null;
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
    public $requestedBudget = 0.00;
    public $notes = null;
    public $isFinal = 'PREPARING';
    public $hasServices = false;


    protected $rules = [
        'selectedEventId' => 'required|exists:banquet_events,id',
        'documentNumber'   => 'nullable|unique:banquet_procurements,document_number',
        'selectedApprover' => 'required|exists:employees,id',
        'selectedReviewer' => 'required|exists:employees,id',
        'requestedBudget'     => 'required|numeric|min:1.00',
        'notes'             => 'nullable|max:500',
        'isFinal'          => 'required|in:PENDING,PREPARING',
    ];

    protected $messages = [
        'selectedEventId.required' => 'Please select an event.',
        'selectedEventId.exists' => 'The selected event does not exist.',
        'selectedApprover.required' => 'Please select an approver.',
        'selectedApprover.exists' => 'The selected approver does not exist.',
        'selectedReviewer.required' => 'Please select a reviewer.',
        'selectedReviewer.exists' => 'The selected reviewer does not exist.',
    ];

    public function render()
    {
        return view('livewire.banquet.banquet-procurement-create');
    }

    public function mount(Request $request)
    {
        // Check if a proposal ID is passed in the request
        if ($request->has('proposal-id')) {
            $this->fetchData();
            $this->isNewRequest = false; // Set to false since we are editing an existing proposal
            $proposalId = $request->input('proposal-id');
            $this->requestReferenceNumber = $request->input('reference');
            $proposal = BanquetProcurement::where('id', $proposalId)->first();
            if ($proposal) {
                $this->banquetEventBudget = $proposal;
                $this->bebId = $proposalId;
                $this->selectedEventId = $proposal->event_id;
                $this->documentNumber = $proposal->document_number;
                $this->selectedApprover = $proposal->approved_by;
                $this->selectedReviewer = $proposal->noted_by;
                $this->requestedBudget = $proposal->suggested_amount;
                $this->hasServices = $proposal->services_included ? true : false;
                $this->notes = $proposal->notes;
                $this->isFinal = $proposal->status;
                $this->requestReferenceNumber = $proposal->reference_number;
                $this->loadEventDetails($this->selectedEventId);
                $this->updatedHasServices();
            } else {
                session()->flash('error', 'Invalid event selected.');
                return redirect()->to('/banquet-procurement-lists');
            }
        } else {
            $this->fetchData();
        }
      
    }

    public function fetchData()
    {
        $this->events = BanquetEvent::query()
            ->with(['customer', 'eventVenues.venue', 'eventServices', 'eventMenus', 'purchaseOrders'])
            ->where('branch_id', auth()->user()->branch_id)
            ->whereIn('status', ['CONFIRMED', 'CLOSED'])
            ->whereDoesntHave('procurements', function ($query) {
                $query->where('branch_id', auth()->user()->branch_id)
                    ->whereIn('status', ['PENDING', 'PREPARING', 'APPROVED']);
            })
            ->get();
        $moduleId = Module::where('module_name', 'Banquet Procurement')->value('id');
        $this->approvers = Signatory::with('employees')->where('signatory_type', 'APPROVER')
            ->where('module_id', $moduleId)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();
        $this->reviewers = Signatory::with('employees')->where('signatory_type', 'REVIEWER')
            ->where('module_id', $moduleId)
            ->where('branch_id', auth()->user()->branch_id)
            ->get();
    }

    public function loadEventDetails($eventId)
    {
        $this->selectedEvent = BanquetEvent::with('purchaseOrders','customer', 'eventVenues', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel')->find($eventId);
        $this->selectedEventId = $this->selectedEvent->id;
        if ($this->selectedEvent) {
            $this->updatedHasServices();
            $this->dispatch('closeSelectEventModal');
            return $this->selectedEvent;
        } else {
            $this->dispatch('closeSelectEventModal');
            session()->flash('error', 'Invalid event selected.');
            return null;
        }
    }

    public function storeRequestBudget()
    {
        // conver number_format to float
        $this->requestedBudget = floatval(str_replace(',', '', $this->requestedBudget));
        $this->validate();
        $currentYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = BanquetProcurement::where('branch_id', $branchId)
            ->whereYear('created_at', $currentYear)
            ->count();
        $this->referenceNumber = 'BEB-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        
        $banquetProcurement = BanquetProcurement::create([
            'event_id' => $this->selectedEventId,
            'document_number' => $this->documentNumber,
            'reference_number' => $this->referenceNumber,
            'status' => $this->isFinal,
            'branch_id' => auth()->user()->branch_id,
            'notes' => $this->notes,
            'approved_by' => $this->selectedApprover,
            'noted_by'  => $this->selectedReviewer,
            'suggested_amount' => $this->requestedBudget,
            'created_by'    => auth()->user()->emp_id,
            'created_at'    => now('Asia/Manila'),
            'services_included' => $this->hasServices ? 1 : 0,
        ]);

        $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Successfully Created.']);
        $this->fetchData();
    }

    public function updateRequest()
    {
        $banquetProcurement = BanquetProcurement::where('event_id', $this->selectedEventId)->where('reference_number', $this->requestReferenceNumber)->first();
        $this->requestedBudget = floatval(str_replace(',', '', $this->requestedBudget));
        if ($banquetProcurement) {
            $this->validate(
            [
                'documentNumber' => 'nullable|string|max:255|unique:banquet_procurements,id,' . ($banquetProcurement ? $banquetProcurement->id : 'NULL'),
                'selectedEventId' => 'required|exists:banquet_events,id',
                'selectedApprover' => 'required|exists:employees,id',
                'selectedReviewer' => 'required|exists:employees,id',
                'requestedBudget' => 'required|numeric',
                'notes' => 'nullable|max:200',
                'isFinal' => 'required|in:PENDING,PREPARING',
            ]
        );
            $banquetProcurement->update([
                'document_number' => $this->documentNumber,
                'status' => $this->isFinal,
                'notes' => $this->notes,
                'approved_by' => $this->selectedApprover,
                'noted_by'  => $this->selectedReviewer,
                'suggested_amount' => $this->requestedBudget,
                'services_included' => $this->hasServices ? 1 : 0,
                'updated_at'    => now('Asia/Manila'),
            ]);
            $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Successfully Updated.']);
            $this->fetchData();
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'Banquet Procurement request not found for the selected event.']);
        }
    }

    public function printPreview()
    {
        redirect()->to('/budget-proposal-print-preview?reference=' . $this->requestReferenceNumber . '&budget-id=' . BanquetProcurement::where('reference_number', $this->requestReferenceNumber)->value('id') );
    }

    public function updatedHasServices()
    {
        if($this->hasServices && $this->selectedEvent){
            $total = 0;
             $total +=
             isset($this->selectedEvent) && $this->selectedEvent->eventMenus ? 
             $this->selectedEvent->eventMenus->sum(function($menu) {
                    return $menu->price->amount * ($menu->qty ? $menu->qty : 1); }): 0;
                $total += isset($this->selectedEvent) && $this->selectedEvent->eventServices ?
                $this->selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
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

    public function updatedRequestedBudget()
    {
        if(!$this->selectedEvent){
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'Please select an event first!']);
            return;
        }
        if($this->requestedBudget > 0 && $this->totalGrossOrder > 0){
            $this->totalPercentage = ( $this->requestedBudget / $this->totalGrossOrder) * 100;
            $this->totalPercentage = number_format($this->totalPercentage, 2);
        }else{
            return;
        }
    }
    public function updatedTotalPercentage()
    {
        if(!$this->selectedEvent){
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'Please select an event first!']);
            return;
        }
        if($this->totalPercentage > 0){
            $this->requestedBudget = ($this->totalPercentage / 100) * $this->totalGrossOrder;
            $this->requestedBudget = number_format($this->requestedBudget, 2);
        }else if(!$this->isNewRequest && $this->requestedBudget > 0){
            $this->totalPercentage = ( $this->requestedBudget / $this->totalGrossOrder) * 100;
            $this->totalPercentage = number_format($this->totalPercentage, 2);
           
        }
    }

}
