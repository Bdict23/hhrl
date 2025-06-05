<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\Venue as VenueModel; // Assuming Venue model exists in App\Models namespace
use App\Models\PriceLevel; // Assuming PriceLevel model exists in App\Models namespace
use App\Models\Service; // Assuming Service model exists in App\Models namespace
use App\Models\Menu; // Assuming Menu model exists in App\Models namespace
use App\Models\Customer;
use App\Models\BranchMenu; 

class BanquetEventCreate extends Component
{
    public $venues = [];
    public $services = [];
    public $menus;
    public $customers = [];

    //selected customer
    public $selectedCustName;
    public $selectedCustID;

    //selected services
    public $selectedServices = [];
    public $servicesAdded = [];
    public $serviceQty = [];
    //selected menus
    public $selectedMenus = [];
    public $menusAdded = [];
    public $menuQty = [];

    // customer registration
    public $customerFirstName;
    public $customerMiddleName;
    public $customerSuffix;
    public $customerLastName;
    public $customerEmail;
    public $customerPhone;
    public $customerGender;
    public $customerAddress;
    public $customerBirthdate;


    public $venue_id;
    public function render()
    {
        return view('livewire.banquet.banquet-event-create');
    }

    public function mount()
    {
        $this->fetchData();
        
    }

    public function fetchData()
    {
        $this->venues = VenueModel::with('ratePrice')
            ->where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->get();
        $this->services = Service::where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->with('ratePrice')
            ->get();
        $company_menus = Menu::where('recipe_type', 'Banquet')
            ->where('company_id', auth()->user()->branch->company_id)
            ->pluck('id')
            ->toArray();
        $this->menus = BranchMenu::whereIn('menu_id', $company_menus)
            ->where('branch_id', auth()->user()->branch_id)
            ->with([
                'menu' => function ($query) {
                    $query->where('recipe_type', 'Banquet')->with('mySRP');
                }
            ])
            ->get();
            // dd($this->menus);
        $this->customers = Customer::where('branch_id', auth()->user()->branch_id)->get();

    }

    public function registerCustomer()
    {
        $this->validate([
            'customerFirstName' => 'required|string|max:255',
            'customerMiddleName' => 'nullable|string|max:255',
            'customerSuffix' => 'nullable|string|max:10',
            'customerLastName' => 'required|string|max:255',
            'customerEmail' => 'nullable|email|max:255',
            'customerPhone' => 'nullable|string|max:20',
            'customerGender' => 'nullable|string|in:Male,Female,Neutral',
            'customerAddress' => 'nullable|string|max:255',
            'customerBirthdate' => 'nullable|date',
        ]);
        $customer = Customer::create([
            'customer_fname' => strtoupper($this->customerFirstName),
            'customer_mname' => strtoupper($this->customerMiddleName),
            'customer_lname' => strtoupper($this->customerLastName),
            'suffix' => strtoupper($this->customerSuffix),
            'email' => strtolower($this->customerEmail),
            'contact_no_1' => $this->customerPhone,
            'gender' => strtoupper($this->customerGender),
            'customer_address' => strtoupper($this->customerAddress),
            'birthday' => $this->customerBirthdate,
            'branch_id' => auth()->user()->branch_id,
        ]);

        $this->selectedCustName = $customer->customer_fname . ' ' . $customer->customer_mname . ' ' . $customer->customer_lname;
        $this->selectedCustID = $customer->id;

        $this->fetchData();
        session()->flash('success', 'Customer successfully registered');
        $this->dispatch('hideCustomerRegistrationModal');
        $this->reset([
            'customerFirstName',
            'customerMiddleName',
            'customerSuffix',
            'customerLastName',
            'customerEmail',
            'customerPhone',
            'customerGender',
            'customerAddress',
            'customerBirthdate',
        ]);

    }

    public function resetForm()
    {
        $this->reset();
        $this->fethData();
    }


    public function  selectCustomer($id, $name){
        $this->selectedCustID = $id;
        $this->selectedCustName = $name;
         $this->dispatch('hideCustomerListModal');

    }


    public function selectService($id){
       $service = Service::with('ratePrice')->find($id);
        if (!$service) {
            return;
        }
        // Check if the item is already in the selected items
        foreach ($this->selectedServices as $selectedItem) {
            if ($selectedItem->id === $service->id) {
                session()->flash('error','Item already selected.');
                return;
            }
        }
        // Add the item to the selected items
        $this->selectedServices[] = $service;
        // Initialize the requested quantity for the item
        $this->servicesAdded[] = ['id' => $service->id, 'qty' => 1 , 'rate' => $service->ratePrice->id ?? null];
    }

    public function removeService($index){
        unset($this->selectedServices[$index]);
        unset($this->servicesAdded[$index]);
        $this->selectedServices = array_values($this->selectedServices);
        $this->servicesAdded = array_values($this->servicesAdded);
    }

    public function selectMenu($id)
    {
        $menu = Menu::with('mySRP')->find($id);
        if (!$menu) {
            return;
        }
        if ($menu->mySRP == null) {
            session()->flash('error', 'Menu has no price.');
            return;
        }
        // Check if the item is already in the selected items
        foreach ($this->selectedMenus as $selectedItem) {
            if ($selectedItem->id === $menu->id) {
                session()->flash('error','Item already selected.');
                return;
            }
        }
        // Add the item to the selected items
        $this->selectedMenus[] = $menu;
        // Initialize the requested quantity for the item
        $this->menusAdded[] = ['id' => $menu->id, 'qty' => 1, 'rate' => $menu->mySRP->id ?? null];
    }

    public function removeMenu($index)
    {
        unset($this->selectedMenus[$index]);
        unset($this->menusAdded[$index]);
        $this->selectedMenus = array_values($this->selectedMenus);
        $this->menusAdded = array_values($this->menusAdded);
    }
}
