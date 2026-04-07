<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
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
use App\Models\Venue; 
use App\Models\EventVenue; 
use App\Models\Signatory;
use App\Models\Module;

class BanquetEventCreate extends Component
{
    public $reference;
    public $services = [];
    public $menus;
    public $alaCarteMenus;
    public $banquetMenus;
    public $customers = [];
    public $venues = [];
    public $reviewers = [];
    public $approvers = [];

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

    //selected venues
    public $selectedVenues = [];
    public $venuesAdded = [];
    public $venueQty = [];

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
    public $event_start_date;
    public $event_end_date;
    public $arrival_time;
    public $departure_time;
    public $event_address;
    public $guest_count;
    public $event_notes;
    public $is_editing = false;
    public $saveAs = 'DRAFT';
    public $currentMenuNote = '';
    public $currentMenuNoteIndex = null;
    public $event_id;
    public $selectedReviewerID;
    public $selectedApproverID;

    protected $rules = [
        'event_name' => 'required|string|max:255',
        'event_start_date' => 'required|date',
        'event_end_date' => 'required|date|after_or_equal:event_start_date',
        'arrival_time' => 'required|date_format:H:i',
        'departure_time' => 'required|date_format:H:i',
        'event_address' => 'required|string|max:255',
        'guest_count' => 'required|integer|min:1',
        'event_notes' => 'nullable|string|max:500',
        'selectedCustID' => 'required|exists:customers,id',
        'selectedReviewerID' => 'required|exists:employees,id',
        'selectedApproverID' => 'required|exists:employees,id',
        
    ];

    protected $messages = [
        'selectedCustID.required' => 'Please select a customer for the event.',
        'selectedCustID.exists' => 'The selected customer does not exist.',
        'selectedReviewerID.required' => 'Please select a reviewer for the event.',
        'selectedReviewerID.exists' => 'The selected reviewer does not exist.',
        'selectedApproverID.required' => 'Please select an approver for the event.',
        'selectedApproverID.exists' => 'The selected approver does not exist.',
    ];

    public function render()
    {
        return view('livewire.banquet.banquet-event-create');
    }

    public function mount(Request $request = null)
    {
        if(auth()->user()->employee->getModulePermission('Purchase Receive') != 2 ){
            if ($request->has('event-id')) {
                 $this->fetchData();
                $this->editEvent($request->query('event-id'));
            }
           $this->fetchData();
        }else{
            return redirect()->to('dashboard');
        }
      
        
    }

    public function fetchData()
    {
        $this->event_address = auth()->user()->branch->branch_address;
        $this->venues = Venue::where('branch_id', auth()->user()->branch_id)->where('status', 'active')->get(); // sunod e fetch tong dili occupied nga venues
        $this->services = Service::where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->with('ratePrice')
            ->get();
       $this->menus = BranchMenuRecipe::with([
        // 'branchMenu' => function ($q) {
        //     $q->where('branch_id', auth()->user()->branch_id)
        //       ->where('is_available', 1);
        // },
        'menu' => function ($q) {
            $q
            // ->where('recipe_type', 'Banquet')
              ->where('status', 'available')
              ->with('mySRP');
        }
        ])
        ->whereHas('branchMenu', function ($q) {
            $q->where('branch_id', auth()->user()->branch_id)
            ->where('is_available', 1);
        })
        ->whereHas('menu', function ($q) {
            $q
            //->where('recipe_type', 'Banquet')
            ->where('status', 'available');
        })
        ->get();
        
        $this->banquetMenus = $this->menus->where('menu.recipe_type', 'Banquet');
        $this->alaCarteMenus = $this->menus->where('menu.menu_type', 'Ala Carte');

        $moduleId = Module::where('module_name', 'Banquet Events')->first()->id;
        $this->customers = Customer::where('branch_id', auth()->user()->branch_id)->get();
        $this->reviewers = Signatory::where('branch_id', auth()->user()->branch_id)->where('module_id', $moduleId)->where('signatory_type', 'REVIEWER')->get();
        $this->approvers = Signatory::where('branch_id', auth()->user()->branch_id)->where('module_id', $moduleId)->where('signatory_type', 'APPROVER')->get();
    }

    public function createEvent()
    {
        if ( $this->is_editing ) {
            $this->updateEvent();
            return;
        }
        $this->validate();
        if($this->event_start_date == $this->event_end_date){
            if($this->arrival_time >= $this->departure_time){
                $this->addError('departure_time', 'Departure time must be after arrival time when the event starts and ends on the same day.');
                return;
            }
        }

        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = BanquetEvent::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'BEO-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        // Create the banquet event
        $event = auth()->user()->branch->banquetEvents()->create([
            'event_name' => $this->event_name,
            'start_date' => $this->event_start_date,
            'end_date' => $this->event_end_date,
            'arrival_time' => $this->arrival_time,
            'departure_time' => $this->departure_time,
            'event_address' => $this->event_address,
            'guest_count' => $this->guest_count,
            'status' => $this->saveAs == 'FINAL' ? 'CONFIRMED' : 'PENDING',
            'notes' => $this->event_notes,
            'reference' => $reference,
            'customer_id' => $this->selectedCustID,
            'branch_id' => auth()->user()->branch_id,
            'created_by' => auth()->user()->id,
            'reviewer_id' => $this->selectedReviewerID,
            'approver_id' => $this->selectedApproverID,
            'created_by' => auth()->user()->emp_id,
        ]);

        $total = 0;
        //insert venue
        if(count($this->venuesAdded)>0){
            foreach($this->venuesAdded as $venue){
                EventVenue::create([
                    'event_id' =>$event->id,
                    'venue_id' => $venue['id'],
                    'price_id' => $venue['price_level_id'],
                    'qty' => $venue['qty'],
                    'start_date' => $this->event_start_date,
                    'end_date' => $this->event_end_date,
                    'start_time' => $this->arrival_time,
                    'end_time' =>  $this->departure_time,
                    'total_amount' => $venue['rate_amount'] * $venue['qty'],
                ]);

                $total += ($venue['rate_amount'] * $venue['qty']);
            }
        }
        // insert services on event_services table
        if (count($this->servicesAdded) > 0) {
            foreach($this->servicesAdded as $service) {
                EventService::create([
                    'event_id' => $event->id,
                    'service_id' => $service['id'],
                    'qty' => $service['qty'],
                    'price_id' => $service['rate'],
                    'total_amount' => $service['rate_amount'] * $service['qty'],
                ]);
                $total += ($service['rate_amount'] * $service['qty']);
            }
        }
        // insert event menu
        if (count($this->menusAdded) > 0) {
            foreach($this->menusAdded as $menu) {
                EventMenu::create([
                    'event_id' => $event->id,
                    'menu_id' => $menu['id'],
                    'note' => $menu['note'] ?? '',
                    'qty' => $menu['qty'],
                    'price_id' => $menu['rate'],
                    'total_amount' => $menu['rate_amount'] * $menu['qty'],
                ]);
                $total += ($menu['rate_amount'] * $menu['qty']);
            }
        }
         $event->update([
                'total_amount' => $total,
            ]);
        $this->reset();
        $this->fetchData();
        $this->dispatch('refresh');
        $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Event successfully added!', 'title' => 'Success']);
    
    }

    public function editMenuNote($index) 
    {
        $menu = $this->selectedMenus[$index] ?? null;
        if (!$menu) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Menu not found.', 'title' => 'Error']);
            return;
        }
        $this->currentMenuNote = $this->menusAdded[$index]['note'] ?? '';
        $this->currentMenuNoteIndex = $index;
    }

    public function updateMenuNote()
    {
        if (isset($this->menusAdded[$this->currentMenuNoteIndex])) {
            $this->menusAdded[$this->currentMenuNoteIndex]['note'] = $this->currentMenuNote;
                $this->dispatch('hideMenuNoteModal');
        }
    }

    public function editEvent($eventId)
    {
        $event = BanquetEvent::with('customer', 'eventServices', 'eventMenus')
            ->where('branch_id', auth()->user()->branch_id)
            ->find($eventId);
        if (!$event) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Event not found.', 'title' => 'Error']);
            return;
        }
        $this->event_id = $event->id;
        $this->reference = $event->reference;
        // Populate event details
        if($event->status == 'PENDING'){
            $this->is_editing = true;
        }else{
            $this->is_editing = false;
        }
        $this->event_name = $event->event_name;
        $this->event_start_date = $event->start_date;
        $this->event_end_date = $event->end_date;
        $this->arrival_time = $event->arrival_time;
        $this->departure_time = $event->departure_time;
        $this->event_address = $event->event_address;
        $this->guest_count = $event->guest_count;
        $this->event_notes = $event->notes;
        $this->selectedReviewerID = $event->reviewer_id;
        $this->selectedApproverID = $event->approver_id;

        // Populate selected customer
        if ($event->customer) {
            $this->selectedCustID = $event->customer->id;
            $this->selectedCustName = $event->customer->customer_fname . ' ' . $event->customer->customer_mname . ' ' . $event->customer->customer_lname;
        }

        // Populate venues
        foreach($event->eventVenues as $eventVenue){
            $venue = Venue::with('ratePrice')->find($eventVenue->venue_id);
            if($venue){
                $this->selectedVenues[] = $venue;
                $this->venuesAdded[] = [
                    'id' => $venue->id,
                    'qty' => $eventVenue->qty,
                    'price_level_id' => $eventVenue->ratePrice->id ?? $venue->ratePrice->id ?? null,
                    'rate_amount' => $eventVenue->ratePrice->amount ?? $venue->ratePrice->amount ?? 0,
                ];
            }
        }

        // Populate selected services
        foreach ($event->eventServices as $eventService) {
            $service = Service::with('ratePrice')->find($eventService->service_id);
            if ($service) {
                $this->selectedServices[] = $service;
                $this->servicesAdded[] = [
                    'id' => $service->id,
                    'qty' => $eventService->qty,
                    'note' => $eventService->note ?? '',
                    'rate' => $eventService->price_id ?? $service->ratePrice->id ?? null,
                    'rate_amount' => $eventService->price->amount ??  $service->ratePrice->amount ?? 0,
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
                    'note' => $eventMenu->note ?? '',
                    'rate' => $eventMenu->price_id ?? $menu->mySRP->id ?? null,
                    'rate_amount' => $eventMenu->price->amount ?? $menu->mySRP->amount ?? 0,
                ];
            }
        }
    }

    public function updateEvent()
    {
        // format arrival and departure time to H:i format to pass validation
        $this->arrival_time = \Carbon\Carbon::parse($this->arrival_time)->format('H:i');
        $this->departure_time = \Carbon\Carbon::parse($this->departure_time)->format('H:i');
        $this->validate();
        // Find the banquet event
        $event = BanquetEvent::where('branch_id', auth()->user()->branch_id)
            ->where('id', $this->event_id)
            ->first();

        if (!$event) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Event not found.', 'title' => 'Error']);
            return;
        }

        $total = 0;
        // Update the banquet event details
        $event->update([
            'event_name' => $this->event_name,
            'start_date' => $this->event_start_date,
            'end_date' => $this->event_end_date,
            'arrival_time' => $this->arrival_time,
            'departure_time' => $this->departure_time,
            'event_address' => $this->event_address,
            'guest_count' => $this->guest_count,
            'status' => $this->saveAs == 'DRAFT' ? 'PENDING' : 'CONFIRMED',
            'notes' => $this->event_notes,
            'customer_id' => $this->selectedCustID,
            'reviewer_id' => $this->selectedReviewerID,
            'approver_id' => $this->selectedApproverID,
        ]);
        // Remove existing services and menus
        EventService::where('event_id', $event->id)->delete();
        EventMenu::where('event_id', $event->id)->delete();
        EventVenue::where('event_id', $event->id)->delete();


        // Re-insert venues
        if(count($this->venuesAdded)>0){
            foreach($this->venuesAdded as $venue){
                EventVenue::create([
                    'event_id' =>$event->id,
                    'venue_id' => $venue['id'],
                    'price_id' => $venue['price_level_id'],
                    'qty' => $venue['qty'],
                    'start_date' => $this->event_start_date,
                    'end_date' => $this->event_end_date,
                    'start_time' => $this->arrival_time,
                    'end_time' =>  $this->departure_time,
                    'total_amount' => $venue['rate_amount'] * $venue['qty'],
                ]);
                $total += ($venue['rate_amount'] * $venue['qty']);
            }
        }

         // insert services on event_services table
        if (count($this->servicesAdded) > 0) {
            foreach($this->servicesAdded as $service) {
                EventService::create([
                    'event_id' => $event->id,
                    'service_id' => $service['id'],
                    'qty' => $service['qty'],
                    'price_id' => $service['rate'],
                    'total_amount' => $service['rate_amount'] * $service['qty'],
                ]);
                $total += ($service['rate_amount'] * $service['qty']);
            }
        }
        // insert event menu
        if (count($this->menusAdded) > 0) {
            foreach($this->menusAdded as $menu) {
                EventMenu::create([
                    'event_id' => $event->id,
                    'menu_id' => $menu['id'],
                    'note' => $menu['note'] ?? '',
                    'qty' => $menu['qty'],
                    'price_id' => $menu['rate'],
                    'total_amount' => $menu['rate_amount'] * $menu['qty'],
                ]);
                $total += ($menu['rate_amount'] * $menu['qty']);
            }  
        }
      $event->update([
                'total_amount' => $total,
            ]);    
        $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Event successfully updated!', 'title' => 'Success']);
        $this->reset();
        $this->dispatch('refresh');
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
                session()->flash('service_error','Item already selected.');
                return;
            }
        }
        // Add the item to the selected items
        $this->selectedServices[] = $service;
        // Initialize the requested quantity for the item
        $this->servicesAdded[] = [
            'id' => $service->id, 
            'qty' => 1, 
            'rate' => $service->ratePrice->id ?? null,
            'rate_amount' => $service->ratePrice->amount ?? 0
        ];
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
            session()->flash('menu_error', 'Menu has no price.');
            return;
        }
        // Check if the item is already in the selected items
        foreach ($this->selectedMenus as $selectedItem) {
            if ($selectedItem->id === $menu->id) {
                session()->flash('menu_error','Item already selected.');
                return;
            }
        }
        // Add the item to the selected items
        $this->selectedMenus[] = $menu;
        // Initialize the requested quantity for the item
        $this->menusAdded[] = [
            'id' => $menu->id, 
            'qty' => 1, 
            'rate' => $menu->mySRP->id ?? null,
            'rate_amount' => $menu->mySRP->amount ?? 0
            ];
    }

    public function removeMenu($index)
    {
        unset($this->selectedMenus[$index]);
        unset($this->menusAdded[$index]);
        $this->selectedMenus = array_values($this->selectedMenus);
        $this->menusAdded = array_values($this->menusAdded);
    }

    public function selectVenue($id)
    {
        $venue = Venue::find($id);
        if (!$venue) {
            return;
        }
        // Check if the item is already in the selected items
        foreach ($this->selectedVenues as $selectedItem) {
            if ($selectedItem->id === $venue->id) {
                session()->flash('venue_error', $venue->venue_name . ' already selected.');
                return;
            }
        }
        // Add the item to the selected items
        $this->selectedVenues[] = $venue;
        // Initialize the requested quantity for the item
        $this->venuesAdded[] = 
            [
            'id' => $venue->id, 
            'qty' => 1,
            'price_level_id' => $venue->ratePrice->id,
            'rate_amount' => $venue->ratePrice->amount ?? 0,
            ];
    }

    public function removeVenue($index)
    {
        unset($this->selectedVenues[$index]);
        unset($this->venuesAdded[$index]);
        $this->selectedVenues = array_values($this->selectedVenues);
        $this->venuesAdded = array_values($this->venuesAdded);
    }


    
}
