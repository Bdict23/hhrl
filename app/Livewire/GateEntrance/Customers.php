<?php

namespace App\Livewire\GateEntrance;

use Livewire\Component;
use App\Models\Customer;

class Customers extends Component
{
    public $customers;
    public $fname;
    public $lname;
    public $mname;
    public $gender;
    public $contact_person;
    public $relation;
    public $email;
    public $suffix;
    public $contact_no_1;
    public $contact_no_2;
    public $address;
    public $branch_id;
    public $renderComponent='list';

    public function mount()
    {
        $this->customers = Customer::all();
        $this->branch_id = 1; // Set a default branch ID
    }
  
    public function ChangeComponent($component)
    {
        $this->renderComponent = $component;
    }
    public function render()
    {
        return view('livewire.gate-entrance.customers');
    }
}
