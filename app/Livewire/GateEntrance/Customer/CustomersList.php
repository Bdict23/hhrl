<?php

namespace App\Livewire\GateEntrance\Customer;

use Livewire\Component;
use App\Models\Customer;
use Carbon\Carbon;
class CustomersList extends Component
{
    public $customers;
    public $allcustomers;

    public $search;
    public function mount()
    {
        $this->customers = Customer::with('bookingrecords')->get();
        $this->allcustomers = $this->customers;
    }

    public function updatedSearch()
    {
        try {
            $this->filterCustomer();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to search customers: ' . $e->getMessage());


        }

    }
     public function filterCustomer()
    {
        if (empty($this->search)) {
            $this->allcustomers = $this->customers;
            return;
        }

        $searchTerm = strtolower($this->search);
        $this->allcustomers = $this->customers->filter(function ($item) use ($searchTerm) {
            return stripos($item->customer_fname, $searchTerm) !== false ||
                   stripos($item->customer_fname, $searchTerm) !== false ||
                   stripos($item->description ?? '', $searchTerm) !== false ||
                   stripos((string)$item->price, $searchTerm) !== false;
        });
    }


    public function render()
    {
        return view('livewire.gate-entrance.customer.customers-list');
    }
}
