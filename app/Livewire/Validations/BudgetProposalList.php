<?php

namespace App\Livewire\Validations;

use Livewire\Component;
use App\Models\BanquetProcurement;

class BudgetProposalList extends Component
{
        public $procurementLists = [];
    //custom columns properties
    public $documentNumber = true;
    public $referenceNumber = true;
    public $dateCreated = true;
    public $eventName = true;
    public $status = true;
    public $approvedAmount = false;
    public $suggestedAmount = true;
    public $approvedBy = false;
    public $notedBy = false;
    public $createdBy = false;
    public $notes = false;
    public $customerName = true;

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->procurementLists = BanquetProcurement::with('event')->where('branch_id', auth()->user()->branch_id)->where('status', 'PENDING')->get();
    }
    public function view($id)
    {
        return redirect()->to('/banquet-budget-show?proposal-id=' . $id);
    }
    public function render()
    {
        return view('livewire.validations.budget-proposal-list');
    }
}
