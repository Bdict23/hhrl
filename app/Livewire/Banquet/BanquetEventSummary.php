<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent; 

class BanquetEventSummary extends Component
{
    public $eventLists = [];
    public $selectedEventId = null;
    public $eventDetails = [];
    public $fromDate;
    public $toDate;
    public function render()
    {
        return view('livewire.banquet.banquet-event-summary');
    }
    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->eventLists = BanquetEvent::with('venue')->where('branch_id', auth()->user()->branch_id)->orderBy('created_at', 'asc')->where('event_date', '>=', now())->get();
    }

    public function viewEventDetails($eventId)
    {
        $this->selectedEventId = $eventId;
        $this->eventDetails = BanquetEvent::with('customer', 'venue', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel', 'eventServices.service')->find($eventId);
        // dd($this->eventDetails);
        $this->dispatch('modalOpened');
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

     public function openToEdit($eventId)
    {
        
      //redirect to receiving page with the selected receing id request
      return redirect()->to('/banquet-event-edit?event-id=' . $eventId );
    }
}
