<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;

class Cancelation extends Component
{
    public $from_date;
    public $to_date;
    public $invoices;
    

    public function filterCancelationByDate(){

    }

    public function fetchData(){

    }
    public function mount()
    {
        $this->fetchData();
    }

    public function render()
    {
        return view('livewire.restaurant.cancelation');
    }
}
