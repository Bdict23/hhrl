<?php

namespace App\Livewire\Banquet\Printing;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\BanquetEvent;

class PrintBEO extends Component
{
    public $eventDetails;
    public $totalAmountLocation = 0;
    public $totalAmountService = 0;
    public $totalAmountMenu = 0;

    public function render()
    {
        return view('livewire.banquet.printing.print-b-e-o');
    }
    public function mount(Request $request)
    {
       if($request->has('event-id')) {
           $eventId = $request->query('event-id');
           $this->eventDetails = BanquetEvent::with('customer', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel', 'eventServices.service')->find($eventId);
           $this->calculateTotalAmount();
       } else {
           abort(404, 'Event ID is required');
       }

    }

    public function calculateTotalAmount()
    {

        // Calculate total from event services
        foreach ($this->eventDetails->eventServices as $service) {
            $this->totalAmountService += $service->total_amount;
        }

        // Calculate total from event menus
        foreach ($this->eventDetails->eventMenus as $menu) {
            $this->totalAmountMenu += $menu->total_amount;
        }

        // Calculate total from location rentals
        foreach ($this->eventDetails->eventVenues as $venue) {
            $this->totalAmountLocation += $venue->total_amount;
        }
    }
}
