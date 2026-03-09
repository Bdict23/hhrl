<?php

namespace App\Livewire\Accounting;

use Livewire\Component;

class ChartOfAccountManagement extends Component
{
    public $templates = [];
    
    public function render()
    {
        return view('livewire.accounting.chart-of-account-management');
    }
}
