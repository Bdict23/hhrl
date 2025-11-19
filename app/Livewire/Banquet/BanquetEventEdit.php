<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent;
use App\Models\Venue as VenueModel;
use App\Models\Customer;
use App\Models\Service;
use App\Models\BranchMenuRecipe;

use Illuminate\Http\Request;

class BanquetEventEdit extends Component
{
    public $id;
    public $eventLists = [];
    public $selectedEventId = null;
    public $eventDetails = [];
    public $fromDate;
    public $toDate;
    public $venues = [];
    public $services = [];
    public $menus = [];
    public $customers = [];
    

    public $event_name;
    public $selectedCustName;
    public $event_date;
    public $event_start_time;
    public $event_end_time;
    public $venue_id;
    public $guest_count;
    public $event_notes;


    public function render()
    {
        return view('livewire.banquet.banquet-event-edit');
    }

      public function mount(Request $request = null)
    {
        if(auth()->user()->employee->getModulePermission('Purchase Receive') != 2 ){
                    if ($request->has('event-id')) {
                        $this->editReceiveRequest($request->query('event-id'));
                    }
                }else{
                    return redirect()->to('dashboard');
                }
    }

    public function editReceiveRequest($eventId)
    {

        $this->id = $eventId;
        $this->fetchData();
        $this->viewEventDetails($eventId);

    }

     public function viewEventDetails($eventId)
    {
        $this->selectedEventId = $eventId;
        $this->eventDetails = BanquetEvent::with('customer', 'venue', 'eventServices', 'eventMenus', 'eventMenus.menu', 'eventMenus.menu.mySRP', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel', 'eventServices.service')->find($eventId);
        //   dd($this->eventDetails);
        $this->event_name = $this->eventDetails->event_name;
        $this->selectedCustName = $this->eventDetails->customer ? $this->eventDetails->customer->customer_fname .' '. $this->eventDetails->customer->customer_lname : '';
        $this->event_date = $this->eventDetails->event_date;
        $this->event_start_time = $this->eventDetails->start_time;
        $this->event_end_time = $this->eventDetails->end_time;
        $this->venue_id = $this->eventDetails->venue_id;
        $this->guest_count = $this->eventDetails->guest_count;
        $this->event_notes = $this->eventDetails->notes;
    }

    public function filterEvents()
    {
        $this->eventLists = BanquetEvent::with('venue')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereBetween('event_date', [$this->fromDate, $this->toDate])
            ->orderBy('created_at', 'asc')
            ->get();
    }
    public function resetFilters()
    {
        $this->fromDate = null;
        $this->toDate = null;
        $this->fetchData();
        $this->dispatch('modalOpened');
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
}
