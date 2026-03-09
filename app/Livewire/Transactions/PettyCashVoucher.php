<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\PettyCashVoucher as PettyCashVoucherModel;

class PettyCashVoucher extends Component
{
    public $pettyCashVouchers;
    public $fromDate;
    public $toDate;

    public function render()
    {
        return view('livewire.transactions.petty-cash-voucher');
    }
    public function mount()
    {
        $this->pettyCashVouchers = PettyCashVoucherModel::where('branch_id', auth()->user()->employee->branch_id)->get();
    }

    public function search()
    {
        $query = PettyCashVoucherModel::where('branch_id', auth()->user()->employee->branch_id);

        if ($this->fromDate) {
            $query->whereDate('created_at', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('created_at', '<=', $this->toDate);
        }

        $this->pettyCashVouchers = $query->get();
    }
}
