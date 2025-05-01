<?php

namespace App\Livewire\GateEntrance\Customer;

use Livewire\Component;
use App\Models\Customer;

class AddCustomer extends Component
{
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
    public $bday;

    protected $rules = [
        'fname' => 'required',
        'lname' => 'required',
        'mname' => 'nullable',
        'gender' => 'required',
        'contact_person' => 'nullable',
        'relation' => 'nullable',
        'email' => 'nullable|email',
        'contact_no_1' => 'nullable|numeric',
        'contact_no_2' => 'nullable|numeric',
        'address' => 'nullable',
        'branch_id' => 'required|numeric',
        'bday' => 'required|date',
    ];

    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Gate Entrance') != 1){
            return redirect()->to('dashboard');
        }
        $this->branch_id = 1; // Set a default branch ID
        $this->gender = 'MALE'; // Set a default branch ID

    }
    public function submit()
    {
       
            $this->validate();
            Customer::create([
                'customer_fname' => $this->fname,
                'customer_lname' => $this->lname,
                'customer_mname' => $this->mname,
                'contact_person' => $this->contact_person,
                'contact_person_relation' => $this->relation,
                'gender'  => $this->gender,
                'contact_no_1'  => $this->contact_no_1,
                'contact_no_2'  => $this->contact_no_2,
                'customer_address'  => $this->address,
                'email'  => $this->email,
                'tin' => '',
                'branch_id'=> auth()->user()->branch_id,
                'birthday'=> $this->bday,

            ]);

                 $this->reset();
                 $this->mount();

            session()->flash('message', 'Form submitted successfully!');
        
    }
    public function render()
    {
        return view('livewire.gate-entrance.customer.add-customer');
    }
}
