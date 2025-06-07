<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent;

class BanquetProcurementCreate extends Component
{
    public $events = [];
    public $withdrawals = [];
    public $selectedEvent ;
    public function render()
    {
        return view('livewire.banquet.banquet-procurement-create');
    }

    public function mount()
    {
        // Initialization logic can go here if needed
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->events = BanquetEvent::with('customer','venue','eventServices','eventMenus')->where('status', 'pending')->where('event_date', '>=', now())->where('branch_id', auth()->user()->branch_id)->get();
    }

    public function loadEventDetails($eventId)
    {
        $this->selectedEvent = BanquetEvent::with('customer', 'venue', 'eventServices', 'eventMenus','equipmentRequests','withdrawals','withdrawals.cardex.priceLevel')->find($eventId);
        if ($this->selectedEvent && $this->selectedEvent->withdrawals) {
            foreach ($this->selectedEvent->withdrawals as $withdrawal) {
                $total = 0;
                if ($withdrawal->cardex && $withdrawal->cardex->priceLevel) {
                    foreach ($withdrawal->cardex->priceLevel as $price) {
                        $total += $price->amount ?? 0;
                    }
                }
                $withdrawal->total_amount = $total;
            }
        }
    }
}
