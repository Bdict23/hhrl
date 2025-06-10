<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent;
use App\Models\Withdrawal;
use App\Models\Cardex;
use App\Models\EquipmentRequest;
use App\Models\EquipmentRequestAttachment;

class BanquetProcurementCreate extends Component
{
    public $events = [];
    public $withdrawals = [];
    public $withdrawalInfo;
    public $withdrawnItems = [];
    public $selectedEvent;



    // selected equipment
    public $selectedEquipments = [];
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


    public function render()
    {
        return view('livewire.banquet.banquet-procurement-create');
    }

    public function mount()
    {
        // Initialization logic can go here if needed
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->events = BanquetEvent::with('customer','venue','eventServices','eventMenus')->where('status', 'pending')->where('event_date', '>=', now())->where('branch_id', auth()->user()->branch_id)->get();
    }

    public function loadEventDetails($eventId)
    {
        $this->selectedEvent = BanquetEvent::with('customer', 'venue', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel')->find($eventId);
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
        $this->withdrawalInfo = Withdrawal::find($eventId);

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

}
