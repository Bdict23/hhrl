<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Customer as customerModel;
use WireUi\Traits\WireUiActions;


class Customer extends Component
{
    use WireUiActions;

// mount
public $customerList;
public $customerSelected;
public $isNew = true;

// input
public $customerId;
public $firstName;
public $middleName;
public $lastName;
public $suffix;
public $gender;
public $birth;
public $address;
public $email;
public $phone1;
public $phone2;

protected $rules = [
    'firstName' => 'required|string',
    'middleName' => 'nullable|string',
    'lastName' => 'required|string',
    'suffix'=> 'nullable|string|max:5',
    'gender' => 'required|in:MALE,FEMALE,NEUTRAL',
];


public function mount(){
    $this->refresh();
}
public function refresh(){
    $this->reset();
    $this->customerList = customerModel::where('branch_id', auth()->user()->branch_id)->get();

}
    public function render()
    {
        return view('livewire.master-data.customer');
    }

    public function selectedCustomer($customer_id){
        $customer = CustomerModel::where('id', $customer_id)->first();
        if($customer){
            $this->customerSelected = $customer;
            $this->isNew = false;
            $this->customerId = $customer_id;
            $this->firstName = $customer->customer_fname;
            $this->middleName = $customer->customer_mname;
            $this->lastName = $customer->customer_lname;
            $this->suffix = $customer->suffix;
            $this->gender = $customer->gender;
            $this->birth = $customer->birthday;
            $this->address = $customer->customer_address;
            $this->email = $customer->email;
            $this->phone1 = $customer->contact_no_1;
            $this->phone2 = $customer->contact_no_2;
        }
    }

    public function create(){
        $this->validate();

        $customer = new CustomerModel();
        $customer->branch_id = auth()->user()->branch_id;
        $customer->customer_fname = $this->firstName;
        $customer->customer_lname = $this->lastName;
        $customer->customer_mname = $this->middleName;
        $customer->suffix = $this->suffix;
        $customer->gender = $this->gender;
        $customer->email = $this->email;
        $customer->contact_no_1 = $this->phone1;
        $customer->contact_no_2 = $this->phone2;
        $customer->customer_address = $this->address;
        $customer->save();
        $this->notify('Successfuly saved!', 'success', 'New Customer Added Successfully!');
        $this->reset();
        $this->mount();


    }

    public function update(){
        $this->validate();
        $customer = customerModel::find($this->customerId);
        if($customer){
            $customer->update([
                'customer_fname'   => $this->firstName,
                'customer_mname'   => $this->middleName,
                'customer_lname'   => $this->lastName,
                'suffix'           => $this->suffix,
                'gender'           => $this->gender,
                'birthday'         => $this->birth,
                'contact_no_1'     => $this->phone1,
                'contact_no_2'     => $this->phone2,
                'email'            => $this->email,
                'customer_address' => $this->address,
            ]);
            $this->notify('Successfuly updated!', 'success', 'Customer Updated Successfully!');
            $this->refresh();
        }

    }



     public function notify($title, $icon, $description) : void
    {
        $this->notification()->send([
            'icon' => $icon,
            'title' => $title,
            'description' => $description,
        ]);
    }
}
