<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetProcurement;

class BanquetProcurementLists extends Component
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

    public function render()
    {
        return view('livewire.banquet.banquet-procurement-lists');
    }

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->procurementLists = BanquetProcurement::with('event')->where('branch_id', auth()->user()->branch_id)->get();
    }
    public function view($id)
    {
        return redirect()->to('/banquet-procurement-create?proposal-id=' . $id . '&reference='. BanquetProcurement::find($id)->reference_number);
    }
}
