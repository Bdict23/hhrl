<?php

namespace App\Livewire\Banquet\Disbursement;

use Livewire\Component;
use App\Models\AcknowledgementReceipt;

class AcknowledgementReceiptSummary extends Component
{
    public $acknowledgementReceipts;
    public $fromDate;
    public $toDate;
    public $statusCheckValue = 'ALL';
    public $statusCheckOptions = [
        'ALL' => 'ALL',
        'CURRENT' => 'CURRENT',
        'POST-DATED' => 'POST-DATED',
    ];
    public function render()
    {
        return view('livewire.banquet.disbursement.acknowledgement-receipt-summary');
    }
    public function mount()
    {
        $this->acknowledgementReceipts = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)->get();
    }

    public function search()
    {
        $query = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id);
        if ($this->statusCheckValue && $this->statusCheckValue != 'ALL') {
            $query->where('check_status', $this->statusCheckValue);
        }

        if ($this->fromDate) {
            $query->whereDate('created_at', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('created_at', '<=', $this->toDate);
        }

        $this->acknowledgementReceipts = $query->get();
    }
}
