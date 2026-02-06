<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;

class RecipeCountAdjustment extends Component
{

public $selectedItems = [];
public $recipes = [];
public $recipeCount = [];
public $adjustmentType;
public $approver;
public $selected_approver_id;
public $adjustmentRefNumber;
    public function render()
    {
        return view('livewire.restaurant.recipe-count-adjustment');
    }

    public function removeItem($itemId)
    {
        // Remove the item from the selected items
        $this->selectedItems = array_filter($this->selectedItems, function ($item) use ($itemId) {
            return $item->id !== $itemId;
        });

        // Remove the item from the purchase request
        $this->purchaseRequest = array_filter($this->purchaseRequest, function ($item) use ($itemId) {
            return $item['id'] !== $itemId;
        });
    }

     public function addItem($itemId)
    {
        $item = Item::with('costPrice')->find($itemId);

        if (!$item) {

            return;
        }
        if ( $item->costPrice == null) {
            session()->flash('error', 'Item has no cost price.');
            return;
        }

        // Check if the item is already in the selected items
        foreach ($this->selectedItems as $selectedItem) {
            if ($selectedItem->id === $item->id) {
                session()->flash('error','Item already selected.');
                return;
            }
        }

        // Add the item to the selected items
        $this->selectedItems[] = $item;

        // Initialize the requested quantity for the item
        $this->purchaseRequest[] = ['id' => $item->id, 'qty' => 1, 'cost' => $item->costPrice->id];
    }
}
