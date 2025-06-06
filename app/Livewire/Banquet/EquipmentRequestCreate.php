<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\EquipmentRequest;
use App\Models\Item;
use App\Models\BanquetEvent;
use App\Models\Department;
use App\Models\Employee;
use App\Models\DepartmentCardex;
use App\Models\EquipmentHandler;
use App\Models\Category;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\EquipmentRequestAttachment;
use Livewire\WithFileUploads;




class EquipmentRequestCreate extends Component
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

    // request details
     public $attachments = [];
    public $myNote = null;
    public $requestDocumentNumber = null;
    public $departmentId = null;
    public $saveAs = null;

    protected $rules = [
        'inchargedBy' => 'required',
        'approver' => 'required',
        'eventName' => 'required',
        'myNote' => 'nullable|string|max:500',
        'requestDocumentNumber' => 'required|string|max:50',
        'departmentId' => 'required|exists:departments,id',
        'attachments' => 'nullable|array',
        'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5048', // max 5MB for each attachment
        'saveAs' => 'required|in:DRAFT,FINAL',
    ];


    public function render()
    {
        return view('livewire.banquet.equipment-request-create');
    }

    public function mount()
    {
      $this->fetchData();
    }

    public function fetchData()
    {
        $this->equipments = Item::with('category')->where('item_status', 'active')->get();
        $this->categories  = Category::where('status', 'active')->where('company_id', auth()->user()->branch->company_id)->where('category_type', 'ITEM')->get();
        $this->events = BanquetEvent::with('customer','venue')->where('status', 'pending')->get();
        $this->departments = Department::where('department_status', 'active')->where('branch_id', auth()->user()->branch_id)->get();
        $this->employees = Employee::with('position')->where('status', 'active')->where('branch_id', auth()->user()->branch_id)->get();
        $module = Module::where('module_name', 'Create Recipe')->first();
        $this->approvers = Signatory::where([['signatory_type', 'APPROVER'], ['status', 'ACTIVE'], ['MODULE_ID', $module->id ], ['branch_id', auth()->user()->branch_id]])->get();
    }

    public function loadDepartmentEmployees($departments_id)
    {
        $this->departmentId = $departments_id;
        $this->departmentEmployees = Employee::with('position')->where('department_id', $departments_id)
            ->where('status', 'active')
            ->where('branch_id', auth()->user()->branch_id)
            ->get();
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

    public function removeHandlingTeamMember($memberId)
    {
        $this->handlingTeam = array_filter($this->handlingTeam, function ($member) use ($memberId) {
            return $member['id'] !== $memberId;
        });
    }

    public function addHandlingTeamMember($memberId)
    {
        $employee = Employee::find($memberId);
        // Check if the employee is already in the handling team
        if (collect($this->handlingTeam)->contains('id', $employee->id)) {
            session()->flash('error', 'Employee already selected.');
            return; // Employee already exists in the handling team
        }
        if ($employee) {
            $this->handlingTeam[] = [
                'id' => $employee->id,
                'first_name' => $employee->name,
                'last_name' => $employee->last_name,
                'position' => $employee->position->position_name ?? null,
            ];
        }
    }

    public function addEquipment($equipmentId)
    {
        $equipment = Item::with('category')->find($equipmentId);
        if (!$equipment) {
            session()->flash('error', 'Equipment not found.');
            return;
        }
        // Check if the equipment is already in the selected equipments
        foreach ($this->selectedEquipments as $selectedItem) {
            if ($selectedItem->id === $equipment->id) {
                session()->flash('error', 'Equipment already selected.');
                return;
            }
        }
        // Add the equipment to the selected equipments
        $this->selectedEquipments[] = $equipment;
        // Initialize the requested quantity for the equipment
        $this->equipmentQty[] = ['id' => $equipment->id, 'qty' => 1];
    }
    public function removeEquipment($index)
    {
        unset($this->selectedEquipments[$index]);
        $this->selectedEquipments = array_values($this->selectedEquipments);
    }

    public function loadEvent($eventId)
    {
        $this->event = BanquetEvent::with('customer', 'venue')->find($eventId);
        $this->eventId = $this->event->id;
        $this->eventName = $this->event->event_name;
        $this->eventDate = $this->event->event_date;
        $this->eventNote = $this->event->notes;
        $this->eventStartTime = $this->event->start_time;
        $this->eventEndTime = $this->event->end_time;

        $this->dispatch('closeEventModal');
    }

    public function loadItemByCategory($categoryId)
    {
        if(!$categoryId) {
              $this->equipments = Item::with('category')->where('item_status', 'active')->get();
        } else {
            $this->equipments = Item::where('category_id', $categoryId)
                ->where('item_status', 'active')
                ->get();
        }
    }


    public function createRequest()
    {
        $this->validate();
       $monthlyCount = EquipmentRequest::where('branch_id', auth()->user()->branch_id)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        $equipmentRequest = EquipmentRequest::create([
            'reference_number' => 'ER-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($monthlyCount, 2, '0', STR_PAD_LEFT),
            'document_number' => $this->requestDocumentNumber,
            'department_id' => $this->departmentId,
            'event_date' => $this->eventDate,
            'from_time' => $this->eventStartTime,
            'to_time' => $this->eventEndTime,
            'requested_by' => auth()->user()->emp_id,
            'received_by' => $this->inchargedBy,
            'approved_by' => $this->approver,
            'event_id' => $this->eventId,
            'branch_id' => auth()->user()->branch_id,
            'notes' => $this->myNote,
            'status' => $this->saveAs === 'DRAFT' ? 'PREPARING' : 'PENDING',
        ]);

        // // Handle attachments
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                EquipmentRequestAttachment::create([
                    'equipment_request_id' => $equipmentRequest->id,
                    'file_path' => $attachment->store('attachments', 'public'),
                ]);
            }
        }

        // Save selected equipments
        foreach ($this->selectedEquipments as $index => $item) {
            DepartmentCardex::create([
                'department_id' => $this->departmentId,
                'branch_id' => auth()->user()->branch_id,
                'item_id' => $item->id,
                'qty_out' => $this->equipmentQty[$index]['qty'] ?? 0,
                'equipment_request_id' => $equipmentRequest->id,
                'status' => $this->saveAs === 'DRAFT' ? 'TEMP' : 'FINAL',
            ]);
        }
        // Save handling team
        foreach ($this->handlingTeam as $member) {
            EquipmentHandler::create([
                'equipment_request_id' => $equipmentRequest->id,
                'employee_id' => $member['id'],
            ]);
        }

        return redirect()->route('banquet.equipment-request.create')->with('success', 'Equipment request created successfully.');
    }

}
