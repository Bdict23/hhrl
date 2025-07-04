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
    public $isNewRequest = true;
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
        $this->isNewRequest = false;
        $equipmentRequest = EquipmentRequest::with(['attachments', 'department', 'event', 'incharge', 'approver', 'equipmentHandlers','departmentCardex'])
            ->where('reference_number', $referenceNumber)
            ->firstOrFail();
        // dd($equipmentRequest);

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


    public function loadDepartmentEmployees($departments_id)
    {
       
        $this->departmentId = $departments_id;
        $this->departmentEmployees = Employee::with('position')->where('department_id', $departments_id)
            ->where('status', 'active')
            ->where('branch_id', auth()->user()->branch_id)
            ->get();
      if($this->isNewRequest){
        //empty the handling team
        $this->handlingTeam = [];
        $this->handlingTeam = $this->departmentEmployees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'first_name' => $employee->name,
                'last_name' => $employee->last_name,
                'position' => $employee->position->position_name ?? null,

            ];
        })->toArray();
        } 
    }
}
