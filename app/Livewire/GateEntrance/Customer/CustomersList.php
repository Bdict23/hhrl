<?php

namespace App\Livewire\GateEntrance\Customer;

use Livewire\Component;
use App\Models\Customer;
use Carbon\Carbon;
class CustomersList extends Component
{
    public $customers;

    public function mount()
    {
        $this->customers = Customer::with('bookingrecords')->get();
    }
    public function render()
    {
        return view('livewire.gate-entrance.customer.customers-list');
    }
}
