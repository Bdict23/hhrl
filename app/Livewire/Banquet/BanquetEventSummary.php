<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent;
use App\Models\Customer; 
use App\Models\EventMenu;
use App\Models\EventVenue;

class BanquetEventSummary extends Component
{
    public $eventLists = [];
    public $selectedEventId = null;
    public $selectedEventStatus = null;
    public $eventDetails = [];
    public $venueDetails = [];
    public $fromDate;
    public $toDate;
    public $customerDetails;

    
    public function render()
    {
        return view('livewire.banquet.banquet-event-summary');
    }
    public function mount()
    {
        $this->fetchData();
    }

       public function openEvent()
    {
      return redirect()->to('/banquet-events-create?event-id=' .  $this->selectedEventId);
    }

     public function viewCustomer($customerId = null)
    {
         $this->customerDetails = $customerId ? Customer::find($customerId) : null;

    }

    public function fetchData()
    {
        $this->eventLists = BanquetEvent::where('branch_id', auth()->user()->branch_id)
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function viewEventDetails($eventId)
    {
        $this->selectedEventId = $eventId;
        $this->selectedEventStatus = BanquetEvent::find($eventId)->status;
        $this->eventDetails = BanquetEvent::with('customer', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel', 'eventServices.service')->find($eventId);
        $this->venueDetails = EventVenue::with('venue', 'ratePrice')->where('event_id', $eventId)->get();
        $this->viewCustomer($this->eventDetails->customer_id);
        $this->dispatch('showEventDetailsModal');
        $this->dispatch('modalOpened');
    }

    public function filterEvents()
    {
        $this->eventLists = BanquetEvent::where('branch_id', auth()->user()->branch_id)
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

     public function openToEdit($eventId)
    {
        
      //redirect to receiving page with the selected receing id request
      return redirect()->to('/banquet-event-edit?event-id=' . $eventId );
    }

  
    public function confirmEvent()
    {
      $event = BanquetEvent::find($this->selectedEventId);
      if ($event) {
          $event->status = 'CONFIRMED';
          $event->save();
          $this->selectedEventStatus = 'CONFIRMED';
          session()->flash('success', 'Event has been confirmed successfully.');
      }
    }


}
