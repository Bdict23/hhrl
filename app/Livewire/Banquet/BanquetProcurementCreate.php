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
    public $approvedBudget = null; 
    public $notes = null;
    public $isFinal = 'PREPARING';


    protected $rules = [
        'selectedEventId' => 'required|exists:banquet_events,id',
        'documentNumber'   => 'required|unique:banquet_procurements,document_number',
        'selectedApprover' => 'required|exists:employees,id',
        'selectedReviewer' => 'required|exists:employees,id',
        'requestedBudget'     => 'required|numeric',
        'notes'             => 'nullable|max:200',
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
                $this->selectedEventId = $proposal->event_id;
                $this->documentNumber = $proposal->document_number;
                $this->selectedApprover = $proposal->approved_by;
                $this->selectedReviewer = $proposal->noted_by;
                $this->requestedBudget = $proposal->suggested_amount;
                $this->approvedBudget = $proposal->approved_amount;
                $this->notes = $proposal->notes;
                $this->isFinal = $proposal->status;
                $this->requestReferenceNumber = $proposal->reference_number;
                $this->loadEventDetails($this->selectedEventId);
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
        $this->events = BanquetEvent::with('customer','venue','eventServices','eventMenus','purchaseOrders')->where('status', 'CONFIRMED')->where('event_date', '>=', now())->where('branch_id', auth()->user()->branch_id)->get();
        
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

    public  function viewWithdrawal($eventId)
    {
        $this->withdrawnItems = Cardex::with('item')->where('withdrawal_id', $eventId)->get();

    }

    public function viewEquipmentInfo($referenceNumber)
    {

        $equipmentRequest = EquipmentRequest::with(['attachments', 'department', 'event', 'incharge', 'approver', 'equipmentHandlers','departmentCardex']) 
            ->where('reference_number', $referenceNumber)
            ->firstOrFail();

        // Load the data into the component properties
        $this->requestDocumentNumber = $equipmentRequest->document_number;
        $this->requestReferenceNumber = $equipmentRequest->reference_number;
        $this->myNote = $equipmentRequest->notes;
        $this->inchargedBy = $equipmentRequest->incharge ? $equipmentRequest->incharge->name : null;
        $this->approver = $equipmentRequest->approver ? $equipmentRequest->approver->name : null;
        $this->departmentName = $equipmentRequest->department ? $equipmentRequest->department->department_name : null;

        // Load selected equipments
        foreach ($equipmentRequest->departmentCardex as $cardex) {
            if ($cardex->item) {
                $this->selectedEquipments[] = $cardex->item;
                $this->equipmentQty[] = ['id' => $cardex->item_id, 'qty' => $cardex->qty_out];
            }
        }


        // Load handling team
        foreach ($equipmentRequest->equipmentHandlers as $handler) {
            if ($handler->employee) {
                $this->handlingTeam[] = [
                    'id' => $handler->employee_id,
                    'first_name' => $handler->employee->name,
                    'last_name' => $handler->employee->last_name,
                    'position' => optional($handler->employee)->position->position_name ?  : null,
                ];
            }
        }

        // Load attachments
        $imagePaths = EquipmentRequestAttachment::where('equipment_request_id', $equipmentRequest->id)->get('file_path')->toArray();
        $this->attachments = [];
        foreach ($imagePaths as $imagePath) {
            $filePath = storage_path('app/public/' . $imagePath['file_path']);
            // dd($filePath);
            if (file_exists($filePath)) {
                $this->attachments[] = $imagePath['file_path'];
            }
        }
    }

    public function storeRequestBudget()
    {
        $this->validate();
        $currentYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = BanquetProcurement::where('branch_id', $branchId)
            ->whereYear('created_at', $currentYear)
            ->count();
        $this->referenceNumber = 'BB-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        
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
        ]);

        session()->flash('success','Successfully Created.');
        $this->dispatch('clearFields');
        $this->reset();
        $this->fetchData();
    }

    public function updateRequest()
    {
        $banquetProcurement = BanquetProcurement::where('event_id', $this->selectedEventId)->where('reference_number', $this->requestReferenceNumber)->first();
        if ($banquetProcurement) {
            $this->validate(
            [
                'documentNumber' => 'required|string|max:255|unique:banquet_procurements,id,' . ($banquetProcurement ? $banquetProcurement->id : 'NULL'),
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
            ]);
            session()->flash('success', 'Successfully Updated.');
            $this->dispatch('clearFields');
            $this->reset();
            $this->fetchData();
        } else {
            session()->flash('error', 'Banquet Procurement not found.');
        }
    }

    public function printPreview()
    {
        redirect()->to('/budget-proposal-print-preview?reference=' . $this->requestReferenceNumber . '&budget-id=' . BanquetProcurement::where('reference_number', $this->requestReferenceNumber)->value('id') );
    }


}
