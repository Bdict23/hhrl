<?php

namespace App\Livewire\GateEntrance;

use Livewire\Component;
use App\Models\Customer;
use App\Models\BookingRecords;



class GateEntrance extends Component
{
    public $bookingrecords;


    public function mount()
    {
        $this->bookingrecords =  BookingRecords::where('booking_status', 'Active')->get();
    }

    public function render()
    {
        return view('livewire.gate-entrance.gate-entrance');
    }

}
