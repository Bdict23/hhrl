<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\Venue as VenueModel; // Assuming Venue model exists in App\Models namespace
use App\Models\PriceLevel; // Assuming PriceLevel model exists in App\Models namespace
use App\Models\Service; // Assuming Service model exists in App\Models namespace
use App\Models\Menu; // Assuming Menu model exists in App\Models namespace
use App\Models\Customer;
use App\Models\BranchMenu; 

class BanquetEventCreate extends Component
{
    public $venues = [];
    public $services = [];
    public $menus;
    public $customers = [];

    public $venue_id;
    public function render()
    {
        return view('livewire.banquet.banquet-event-create');
    }

    public function mount()
    {
        $this->fetchData();
        
    }

    public function fetchData()
    {
        $this->venues = VenueModel::with('ratePrice')
            ->where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->get();
        $this->services = Service::where([['status', 'active'], ['branch_id', auth()->user()->branch_id]])
            ->with('ratePrice')
            ->get();
        $company_menus = Menu::where('recipe_type', 'Banquet')
            ->where('company_id', auth()->user()->branch->company_id)
            ->pluck('id')
            ->toArray();
        $this->menus = BranchMenu::whereIn('menu_id', $company_menus)
            ->where('branch_id', auth()->user()->branch_id)
            ->with([
                'menu' => function ($query) {
                    $query->where('recipe_type', 'Banquet')->with('mySRP');
                }
            ])
            ->get();
            // dd($this->menus);
        $this->customers = Customer::where('branch_id', auth()->user()->branch_id)->get();

    }
}
