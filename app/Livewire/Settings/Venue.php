<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Venue as venueModel; // Assuming Venue model exists in App\Models namespace
use App\Models\PriceLevel; // Assuming PriceLevel model exists in App\Models namespace

class Venue extends Component
{
    public $venues = [];
    public $venue_id;
    public $venue_name_input;
    public $venue_code_input;
    public $capacity_input;
    public $venue_description_input;
    public $venue_rate_input;
    public $oldPrice;
    public function render()
    {
        return view('livewire.settings.venue');

    }

    public function mount()
    {
     $this->fetchVenues();
    }


    public function fetchVenues()
    {
        $this->venues = venueModel::with('ratePrice')->where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])->get();
    }

    public function storeVenue()
    {
        $this->validate([
            'venue_name_input' => 'required|string|max:255',
            'venue_code_input' => 'required|string|max:50|unique:venues,venue_code',
            'capacity_input' => 'required|integer|min:1',
            'venue_description_input' => 'nullable|string|max:500',
            'venue_rate_input' => 'required|numeric|min:0',
        ]);

        $venue = new venueModel();
        $venue->venue_name = $this->venue_name_input;
        $venue->venue_code = $this->venue_code_input;
        $venue->capacity = $this->capacity_input;
        $venue->description = $this->venue_description_input;
        $venue->status = 'active';
        $venue->branch_id = auth()->user()->branch_id; // Assuming branch_id is set from the authenticated user
        $venue->save();
        // Create a default rate price for the venue
         PriceLevel::create([
            'venue_id' => $venue->id,
            'price_type' => 'RATE',
            'created_by' => auth()->user()->emp_id, // Assuming created_by is set from the authenticated user
            'amount' => $this->venue_rate_input,
            'branch_id' => auth()->user()->branch_id, // Assuming branch_id is set from the authenticated user
        ]);


        $this->fetchVenues();
        session()->flash('success', 'Venue successfully added');
        $this->dispatch('clearForm');
        $this->reset(['venue_name_input', 'venue_code_input', 'capacity_input', 'venue_description_input']);
    }

    public function deactivate($venueId)
    {
        $venue = venueModel::find($venueId);
        if ($venue) {
            $venue->status = 'inactive';
            $venue->save();
            $this->fetchVenues();
            session()->flash('success', 'Venue successfully deactivated');
        } else {
            session()->flash('error', 'Venue not found');
        }
    }



    public function editVenue($venueId)
    {
        $venue = venueModel::find($venueId);
        if ($venue) {
            $this->venue_id = $venue->id;
            $this->venue_name_input = $venue->venue_name;
            $this->venue_code_input = $venue->venue_code;
            $this->capacity_input = $venue->capacity;
            $this->venue_rate_input = $venue->ratePrice ? $venue->ratePrice->amount : 0; // Assuming ratePrice is a relationship that returns the latest price level
            $this->oldPrice = $this->venue_rate_input; // Store the old price for comparison
            $this->venue_description_input = $venue->description;
        } else {
            session()->flash('error', 'Venue not found');
        }
    }


    public function updateVenue()
    {
        $this->validate([
            'venue_name_input' => 'required|string|max:30',
            'venue_code_input' => 'required|string|max:10|unique:venues,venue_code,' . $this->venue_id,
            'capacity_input' => 'required|integer|min:1',
            'venue_description_input' => 'nullable|string|max:500',
            'venue_rate_input' => 'required|numeric|min:0',
        ]);

        $venue = venueModel::find($this->venue_id);
        if ($venue) {
            $venue->update([
                'venue_name' => $this->venue_name_input,
                'venue_code' => $this->venue_code_input,
                'capacity' => $this->capacity_input,
                'description' => $this->venue_description_input,
            ]);
            if ($this->venue_rate_input != $this->oldPrice) {
                
                // create rate price
                PriceLevel::Create(
                    [
                        'venue_id' => $venue->id,
                        'price_type' => 'RATE',
                        'amount' => $this->venue_rate_input,
                        'created_by' => auth()->user()->emp_id, // Assuming created_by is set from the authenticated user
                        'branch_id' => auth()->user()->branch_id, // Assuming branch_id is set from the authenticated user
                    ]
                );
            }
            $this->fetchVenues();
            session()->flash('success', 'Venue successfully updated');
            $this->dispatch('hideUpdateVenueModal');
            $this->reset(['venue_name_input', 'venue_code_input', 'capacity_input', 'venue_description_input']);
        } else {
            session()->flash('error', 'Venue not found');
        }
    }
}
