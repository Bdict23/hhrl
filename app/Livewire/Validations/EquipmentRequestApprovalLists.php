<?php

namespace App\Livewire\Validations;

use Livewire\Component;
use App\Models\EquipmentRequest;

class EquipmentRequestApprovalLists extends Component
{
    public $equipmentRequests = [];
    public function render()
    {
        return view('livewire.validations.equipment-request-approval-lists');
    }

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->equipmentRequests = EquipmentRequest::with(['event', 'incharge', 'approver', 'attachments','department'])
        ->where('branch_id', auth()->user()->branch_id)
        ->where('status', 'PENDING')
        ->where('approved_by', auth()->user()->emp_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function viewRequest($reference)
    {
        return redirect()->to('/equipment-request-approval-show?equipment-request-number=' . $reference);
    }
}
