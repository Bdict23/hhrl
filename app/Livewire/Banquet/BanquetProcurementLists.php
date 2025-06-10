<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetProcurement;

class BanquetProcurementLists extends Component
{
    public $procurementLists = [];
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
        $this->procurementLists = BanquetProcurement::where('branch_id', auth()->user()->branch_id)->get();
    }
}
