<?php

namespace App\Livewire\Validations;

use Livewire\Component;
use App\Models\BatchProperty;

class FixedAssetReviewLists extends Component
{
    public $batches;
    public function render()
    {
        return view('livewire.validations.fixed-asset-review-lists');
    }

    public function mount(){
        $this->fetchData();
    }

    public function fetchData(){
        $this->batches = BatchProperty::where('branch_id', auth()->user()->branch_id)->where('status', 'OPEN')->where('reviewed_date', null)->get();
    }
}
