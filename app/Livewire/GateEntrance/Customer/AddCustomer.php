<?php

namespace App\Livewire\GateEntrance\Customer;

use Livewire\Component;
use App\Models\Customer;
use Carbon\Carbon;

class AddCustomer extends Component
{

    public $lname;
    public $mname;
    public $fname;

    public $gender;
    public $branch_id;
    public $bday;
    public $type;


    public function mount()
    {
        $this->branch_id = 1; // Set a default branch ID
        $this->gender = 'MALE'; // Set a default branch ID
        $this->type = 0;

    }
    public function submit()
    {
        try {
            // Here you can handle the form submission, e.g., save to the database
            // For demonstration, we'll just flash a message
            // You can also use the Customer model to save the data
            if ($this->type == 1) {
                $this->validate([
                    'fname' => 'required',
                    'lname' => 'required',
                    'gender' => 'required',
                    'bday' => 'required|date',

                ]);
                $age = Carbon::parse($this->bday)->age;
                if ($age < 18) {
                    session()->flash('date_error', 'Customer must be at least 18 years old.');
                    return;
                }

                $cust = Customer::create([
                    'customer_fname' => $this->fname,
                    'customer_lname' => $this->lname,
                    'customer_mname' => $this->mname,
                    'gender' => $this->gender,
                    'branch_id' => $this->branch_id,
                    'birthday' => $this->bday,
                ]);
            }
                $id = $this->type==1? $cust->id:0;

                redirect()->route('book.service.page',['id' => $id]);

                // session()->flash('message', 'Customer added successfully.');

        }catch(\Exception $e) {
            session()->flash('message', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.gate-entrance.customer.add-customer');
    }
}
