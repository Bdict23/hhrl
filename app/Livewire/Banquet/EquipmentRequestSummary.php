<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\EquipmentRequest;

class EquipmentRequestSummary extends Component
{

    public $equipmentRequests = [];

    public function render()
    {
        return view('livewire.banquet.equipment-request-summary');
    }
    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->equipmentRequests = EquipmentRequest::with(['event', 'incharge', 'approver', 'attachments','department'])->where('branch_id', auth()->user()->branch_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function viewRequest($reference)
    {
          return redirect()->to('/equipment-request.show?equipment-request-number=' . $reference);
    }
}
