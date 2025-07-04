<?php

namespace App\Livewire\PrintPreview;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\BanquetProcurement;
use App\Models\BanquetEvent;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\Employee;
use App\Models\EquipmentRequest;
use App\Models\EquipmentRequestAttachment;
use App\Models\DepartmentCardex;
use App\Models\Item;
use App\Models\Category;
use App\Models\Department;


class BudgetProposalShow extends Component
{
    public $proposedBudgetId;
    public $requestReferenceNumber;
    public $proposal;
    public $selectedEvent;

    public function render()
    {
        return view('livewire.print-preview.budget-proposal-show');
    }

    public function mount(Request $request)
    {
        // Retrieve the proposal ID from the request query parameters
        $this->proposedBudgetId = $request->query('budget-id');
        $this->requestReferenceNumber = $request->query('reference');
        $this->fetchData();
        
       
    }

    public function fetchData()
    {
        $this->proposal = BanquetProcurement::with(['event', 'approver', 'notedBy'])->find($this->proposedBudgetId);
        $this->selectedEvent = BanquetEvent::with('purchaseOrders','customer', 'venue', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel')->find($this->proposal->event_id);
    }



}
