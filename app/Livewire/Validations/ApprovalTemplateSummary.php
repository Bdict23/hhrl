<?php

namespace App\Livewire\Validations;

use App\Models\Accounting\COATransactionTemplate;

use Livewire\Component;

class ApprovalTemplateSummary extends Component
{
    public  $templates;
    public function render()
    {
        return view('livewire.validations.approval-template-summary');
    }
    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->templates = COATransactionTemplate::where('approved_by', auth()->user()->emp_id)->where('status', 'FINAL')->where('approved_date', null)->get();
    }
}
