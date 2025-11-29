<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\Venue as VenueModel; // Assuming Venue model exists in App\Models namespace
use App\Models\PriceLevel; // Assuming PriceLevel model exists in App\Models namespace
use App\Models\Service; // Assuming Service model exists in App\Models namespace
use App\Models\Menu; // Assuming Menu model exists in App\Models namespace
use App\Models\Customer;
use App\Models\BranchMenu; 
use App\Models\BanquetEvent; // Assuming BanquetEvent model exists in App\Models namespace
use App\Models\EventService;
use App\Models\EventMenu;
use Illuminate\Http\Request;
use App\Models\BranchMenuRecipe; // Assuming BranchMenuRecipe model exists in App\Models namespace

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

    // banquet event details
    public $event_name;
    public $event_date;
    public $event_start_time;
    public $event_end_time;
    public $venue_id;
    public $guest_count;
    public $event_notes;
    public $is_editing = false;
    public $saveAs = 'DRAFT';

    protected $rules = [
        'event_name' => 'required|string|max:255',
        'event_date' => 'required|date',
        'event_start_time' => 'required|date_format:H:i',
        'event_end_time' => 'required|date_format:H:i|after:event_start_time',
        'venue_id' => 'required|exists:venues,id',
        'guest_count' => 'required|integer|min:1',
        'event_notes' => 'nullable|string|max:500',
    ];
    public function render()
    {
        return view('livewire.banquet.banquet-event-create');
    }

    public function mount(Request $request = null)
    {
        if(auth()->user()->employee->getModulePermission('Purchase Receive') != 2 ){
            if ($request->has('event-id')) {
                //  dd($request->query('requisition-id'));
                 $this->fetchData();
                $this->editEvent($request->query('event-id'));
            }
           $this->fetchData();
        }else{
            return redirect()->to('dashboard');
        }
      
        
    }

    public function editEvent($eventId)
    {
        $event = BanquetEvent::with('customer', 'venue', 'eventServices', 'eventMenus')
            ->where('branch_id', auth()->user()->branch_id)
            ->find($eventId);
        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }
        // Populate event details
        if($event->status == 'PENDING'){
            $this->is_editing = true;
        }else{
            $this->is_editing = false;
        }
        $this->event_name = $event->event_name;
        $this->event_date = $event->event_date;
        $this->event_start_time = $event->start_time;
        $this->event_end_time = $event->end_time;
        $this->venue_id = $event->venue_id;
        $this->guest_count = $event->guest_count;
        $this->event_notes = $event->notes;

        // Populate selected customer
        if ($event->customer) {
            $this->selectedCustID = $event->customer->id;
            $this->selectedCustName = $event->customer->customer_fname . ' ' . $event->customer->customer_mname . ' ' . $event->customer->customer_lname;
        }

        // Populate selected services
        foreach ($event->eventServices as $eventService) {
            $service = Service::with('ratePrice')->find($eventService->service_id);
            if ($service) {
                $this->selectedServices[] = $service;
                $this->servicesAdded[] = [
                    'id' => $service->id,
                    'qty' => $eventService->qty,
                    'rate' => $eventService->price_id,
                ];
            }
        }

        // Populate selected menus
        foreach ($event->eventMenus as $eventMenu) {
            $menu = Menu::with('mySRP')->find($eventMenu->menu_id);
            if ($menu) {
                $this->selectedMenus[] = $menu;
                $this->menusAdded[] = [
                    'id' => $menu->id,
                    'qty' => $eventMenu->qty,
                    'rate' => $eventMenu->price_id,
                ];
            }
        }
    }

    public function updateEvent()
    {
        $this->validate();
        // Find the banquet event
        $event = BanquetEvent::where('branch_id', auth()->user()->branch_id)
            ->where('event_name', $this->event_name)
            ->where('event_date', $this->event_date)
            ->first();

        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        // Update the banquet event details
        $event->update([
            'event_name' => $this->event_name,
            'event_date' => $this->event_date,
            'start_time' => $this->event_start_time,
            'end_time' => $this->event_end_time,
            'venue_id' => $this->venue_id,
            'guest_count' => $this->guest_count,
            'status' => $this->saveAs == 'Final' ? 'PENDING' : 'CONFIRMED',
            'notes' => $this->event_notes,
            'customer_id' => $this->selectedCustID,
        ]);
        // Remove existing services and menus
        EventService::where('event_id', $event->id)->delete();
        EventMenu::where('event_id', $event->id)->delete();



         // insert services on event_services table
        if (count($this->servicesAdded) > 0) {
            foreach($this->servicesAdded as $service) {
                EventService::create([
                    'event_id' => $event->id,
                    'service_id' => $service['id'],
                    'qty' => $service['qty'],
                    'price_id' => $service['rate'],
                ]);
            }
        }
        // insert event menu
        if (count($this->menusAdded) > 0) {
            foreach($this->menusAdded as $menu) {
                EventMenu::create([
                    'event_id' => $event->id,
                    'menu_id' => $menu['id'],
                    'qty' => $menu['qty'],
                    'price_id' => $menu['rate'],
                ]);
            }



        session()->flash('success', 'Event successfully updated!');
        $this->reset();
        $this->dispatch('refresh');
    }
    }
  

    public function fetchData()
    {
        $this->venues = VenueModel::with('ratePrice')
            ->where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->get();
        $this->services = Service::where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->with('ratePrice')
            ->get();
       $this->menus = BranchMenuRecipe::with([
        'branchMenu' => function ($q) {
            $q->where('branch_id', auth()->user()->branch_id)
              ->where('is_available', 1);
        },
        'menu' => function ($q) {
            $q->where('recipe_type', 'Banquet')
              ->where('status', 'available')
              ->with('mySRP');
        }
    ])
    ->whereHas('branchMenu', function ($q) {
        $q->where('branch_id', auth()->user()->branch_id)
          ->where('is_available', 1);
    })
    ->whereHas('menu', function ($q) {
        $q->where('recipe_type', 'Banquet')
          ->where('status', 'available');
    })
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
        $this->dispatch('refresh');
        $this->fetchData();
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
        // dd($id);
        $menu = Menu::with('mySRP')->find($id);
        // dd($menu);
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


    public function createEvent()
    {
        if ( $this->is_editing ) {
            $this->updateEvent();
            return;
        }
        $this->validate();

        // Create the banquet event
        $event = auth()->user()->branch->banquetEvents()->create([
            'event_name' => $this->event_name,
            'event_date' => $this->event_date,
            'start_time' => $this->event_start_time,
            'end_time' => $this->event_end_time,
            'venue_id' => $this->venue_id,
            'guest_count' => $this->guest_count,
            'status' => $this->saveAs == 'FINAL' ? 'CONFIRMED' : 'DRAFT',
            'notes' => $this->event_notes,
            'customer_id' => $this->selectedCustID,
            'branch_id' => auth()->user()->branch_id,
            'created_by' => auth()->user()->id,
        ]);

        // insert services on event_services table
        if (count($this->servicesAdded) > 0) {
            foreach($this->servicesAdded as $service) {
                EventService::create([
                    'event_id' => $event->id,
                    'service_id' => $service['id'],
                    'qty' => $service['qty'],
                    'price_id' => $service['rate'],
                ]);
            }
        }
        // insert event menu
        if (count($this->menusAdded) > 0) {
            foreach($this->menusAdded as $menu) {
                EventMenu::create([
                    'event_id' => $event->id,
                    'menu_id' => $menu['id'],
                    'qty' => $menu['qty'],
                    'price_id' => $menu['rate'],
                ]);
            }
        }
        $this->reset();
        session()->flash('success','Event successfully added!');
        $this->fetchData();
        $this->dispatch('refresh');
    }
}
