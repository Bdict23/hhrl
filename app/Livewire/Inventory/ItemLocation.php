<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Location as ItemLocationModel;
use App\Models\Item;

class ItemLocation extends Component
{
    public $items = [];
    public $location = [];

    // Display Item information
    public $itemID;
    public $itemSKU;
    public $itemName;
    public $itemGroupLoc;
    public $itemLocationName;
    public $newItemLocation;
    public $newItemLocationGroup;


    protected $rules = [
        'itemID' => 'required',
        'newItemLocationGroup' => 'nullable|max:50',
        'newItemLocation' => 'required|max:50',
    ];

    public function render()
    {
        return view('livewire.inventory.item-location');
    }

    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Item Location') == 2) {
            abort(403, 'Unauthorized access to this page.');
        }
        $this->fetchData();
    }
    public  function fetchData()
    {
        $this->items = Item::with('location')->where('item_status', 'active')->get();
        $this->location = ItemLocationModel::select('item_id', 'location_name', 'location_group', 'id')
            ->where('branch_id', auth()->user()->branch_id)
            ->get()
            ->groupBy('item_id')
            ->map(function ($item) {
                return [
                    'group_location' => $item->pluck('location_group')->implode(', '),
                    'location_name' => $item->pluck('location_name')->implode(',')
                ];
            });

    }

    public function saveLocation()
    {
        $this->validate();
        $location = ItemLocationModel::updateOrCreate(
            ['item_id' => $this->itemID],
            [
                'location_group' => $this->newItemLocationGroup,
                'location_name' => $this->newItemLocation,
                'branch_id' => auth()->user()->branch_id,
                'employee_id' => auth()->user()->id,
            ]
        );
        // dd($location);
        session()->flash('success', 'Item Location updated successfully.');
        $this->reset();
        $this->dispatch('clearForm');
        $this->fetchData();
    }


    public function editItemLocation($itemId)
    {
        $selectedItem = Item::find($itemId);
        $currentLocation = ItemLocationModel::where('item_id', $itemId)->first();
        $this->itemID = $selectedItem->id;
        $this->itemSKU = $selectedItem->item_code;
        $this->itemName = $selectedItem->item_description;
        $this->itemGroupLoc = $currentLocation->location_group ?? '';
        $this->itemLocationName = $currentLocation->location_name ?? '';
    
        
    }



}
