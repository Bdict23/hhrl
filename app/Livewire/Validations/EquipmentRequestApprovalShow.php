<?php

namespace App\Livewire\Validations;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\EquipmentRequest;
use App\Models\EquipmentRequestAttachment;
use Illuminate\Http\Request;
use App\Models\DepartmentCardex;
use App\Models\Item;
use App\Models\Category;
use App\Models\BanquetEvent;
use App\Models\Employee;
use App\Models\Signatory;
use App\Models\Module;
use App\Models\Department;

class EquipmentRequestApprovalShow extends Component
{

    use WithFileUploads;

    public $equipments = [];
    public $categories = [];
    public $events = [];
    public $departments = [];
    public $employees = [];
    public $approvers = [];
    public $departmentEmployees = [];

    // selected equipment
    public $selectedCategory;
    public $selectedEquipments = [];
    public $equipmentQty = [];

    // selected incharge
    public $inchargedBy = null;
    //selected approver
    public $approver = null;

    // Handling team
    public $handlingTeam = [];
    
    // selected equipment
    public $selectedEquipment = [];

    // selected event
    public $event = [];
    public $eventId = null;
    public $eventName = null;
    public $eventDate = null;
    public $eventStartTime = null;
    public $eventEndTime = null;
    public $eventNote = null;
    public $isApproved = false;
    public $isNewAttachment = true;

    // request details
     public $attachments = [];
    public $myNote = null;
    public $requestDocumentNumber = null;
    public $departmentId = null;
    public $saveAs = null;
    public $requestReferenceNumber = null;
    public function render()
    {
        return view('livewire.validations.equipment-request-approval-show');
    }

    public function mount(Request $request)
    {
        $this->requestReferenceNumber = $request->query('equipment-request-number');
        if ($this->requestReferenceNumber) {
            $this->editEquipmentRequest($this->requestReferenceNumber);
        } else {
            $this->fetchData();
        }
    }

     public function fetchData()
    {
        $this->equipments = Item::with('category')->where('item_status', 'active')->get();
        $this->categories  = Category::where('status', 'active')->where('company_id', auth()->user()->branch->company_id)->where('category_type', 'ITEM')->get();
        $this->events = BanquetEvent::with('customer','venue')->where('status', 'pending')->where('event_date', '>=', now())->where('branch_id', auth()->user()->branch_id)->get();
        $this->departments = Department::where('department_status', 'active')->where('branch_id', auth()->user()->branch_id)->get();
        $this->employees = Employee::with('position')->where('status', 'active')->where('branch_id', auth()->user()->branch_id)->get();
        $module = Module::where('module_name', 'Recipe')->first();
        $this->approvers = Signatory::where([['signatory_type', 'APPROVER'], ['status', 'ACTIVE'], ['MODULE_ID', $module->id ], ['branch_id', auth()->user()->branch_id]])->get();
    }


    public function editEquipmentRequest($referenceNumber)
    {
        $this->fetchData();
        $equipmentRequest = EquipmentRequest::with(['attachments', 'department', 'event', 'incharge', 'approver', 'equipmentHandlers','departmentCardex'])
            ->where('reference_number', $referenceNumber)
            ->firstOrFail();
        // dd($equipmentRequest);
        if ($equipmentRequest->status === 'RELEASED') {
            $this->isApproved = true;
        }

        // Load the data into the component properties
        $this->requestDocumentNumber = $equipmentRequest->document_number;
        $this->requestReferenceNumber = $equipmentRequest->reference_number;
        $this->departmentId = $equipmentRequest->department_id;
        $this->eventId = $equipmentRequest->event_id;
        $this->eventName = $equipmentRequest->event ? $equipmentRequest->event->event_name : null;
        $this->eventDate = $equipmentRequest->event_date;
        $this->eventStartTime = $equipmentRequest->from_time;
        $this->eventEndTime = $equipmentRequest->to_time;
        $this->eventNote = $equipmentRequest->event ? $equipmentRequest->event->notes : null;
        $this->myNote = $equipmentRequest->notes;
        $this->inchargedBy = $equipmentRequest->received_by;
        $this->approver = $equipmentRequest->approved_by;
        $this->saveAs = $equipmentRequest->status === 'PREPARING' ? 'DRAFT' : 'FINAL';

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

        // load department employees
        $this->loadDepartmentEmployees($equipmentRequest->department_id);
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

    public function loadDepartmentEmployees($departmentId)
    {
        $this->departmentEmployees = Employee::where('department_id', $departmentId)
            ->where('status', 'active')
            ->where('branch_id', auth()->user()->branch_id)
            ->get();
    }


    public function updateRequest(){
        $this->validate([
            'eventId' => 'required',
            'eventDate' => 'required|date',
            'eventStartTime' => 'required',
            'eventEndTime' => 'required|after:eventStartTime',
            'inchargedBy' => 'required',
            'approver' => 'required',
            'departmentId' => 'required',
            'selectedEquipments.*.id' => 'required|exists:items,id',
            'equipmentQty.*.qty' => 'required|numeric|min:1',
            'saveAs' => 'required|in:RELEASED,REJECTED',
        ]);
        // dd($this->saveAs);
        $equipmentRequest = EquipmentRequest::where('reference_number', $this->requestReferenceNumber)->firstOrFail();
        $equipmentRequest->update([
            'event_id' => $this->eventId,
            'event_date' => $this->eventDate,
            'from_time' => $this->eventStartTime,
            'to_time' => $this->eventEndTime,
            'received_by' => $this->inchargedBy,
            'approved_by' => $this->approver,
            'department_id' => $this->departmentId,
            'notes' => $this->myNote,
            'status' => $this->saveAs,
        ]);

        if ($this->saveAs === 'RELEASED') {
            DepartmentCardex::where('equipment_request_id', $equipmentRequest->id)
                ->update(['status' => 'FINAL']);
        }
    
        session()->flash('success', 'Equipment request updated successfully.');
        $this->reset();
    }

}
