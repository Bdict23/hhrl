<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\BatchProperty;
use App\Models\BatchPropertyDetail;

class FixedAssetSummary extends Component
{
    public $validBatch;
    public $assets;
    public function render()
    {
        return view('livewire.inventory.fixed-asset-summary');
    }


    public function mount(){
        $this->fetchData();
    }

    public function fetchData(){
        $this->validBatch = BatchProperty::where('branch_id', auth()->user()->branch_id)->where('status', 'CLOSED')->get()->pluck('id');
        $this->assets = BatchPropertyDetail::whereIn('batch_id', $this->validBatch)->get();
    }
}
