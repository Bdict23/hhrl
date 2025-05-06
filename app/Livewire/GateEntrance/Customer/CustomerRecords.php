<?php

namespace App\Livewire\GateEntrance\Customer;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\BookingRecords;

class CustomerRecords extends Component
{
    public $branches;
    public $customer;
    public $id;
    public function mount($id)
    {
        $this->branches = Branch::all();
        $this->customer= Customer::find($id);
    }
    public function render()
    {
        return view('livewire.gate-entrance.customer.customer-records');
    }
}
