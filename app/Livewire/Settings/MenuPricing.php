<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\PriceLevel;
use App\Models\Menu;

class MenuPricing extends Component
{
    public $categories = [];
    public $selectedCategory = null;
    public $menus = [];
    public $recipestWithTotalCost = [];
    public $selectedMenuId = null;
    public $menu_cost_amount = 0;

    public function render()
    {
        return view('livewire.settings.menu-pricing');
    }

    public function mount()
    {
        $this->fetchData();
        // Initialization logic can go here if needed
    }
    public function fetchData()
    {
        $this->categories = Category::where('category_type', 'MENU')
            ->where('status', 'ACTIVE')
            ->get();
        $this->menus = Menu::with(['recipes','recipes.latestItemCost','mySRP'])->where('status', 'available')->get();
        // dd($this->menus);
        $this->recipestWithTotalCost = $this->menus->map(function ($menu) {
            $recipes = $menu->recipes ?? collect(); // Ensure recipes is a collection
            $totalCost = $recipes->sum(function ($recipe) {
                $itemCost = $recipe->latestItemCost?->amount ?? 0;
                return $itemCost * $recipe->qty;
            });
        
            return [
                'menu_id'    => $menu->id,
                'menu_name'  => $menu->menu_name,
                'total_cost' => $totalCost,
            ];
        });
    }

    public function selectedMenuToUpdate($menuId)
    {
        $this->selectedMenuId = $menuId;
    }

    public function addNewMenuCost()
    {
        $this->validate([
            'selectedMenuId' => 'required|exists:menus,id',
            'menu_cost_amount' => 'required|numeric|min:0',
        ]);
        PriceLevel::create([
            'menu_id' => $this->selectedMenuId,
            'amount' => $this->menu_cost_amount,
            'branch_id' => auth()->user()->branch_id,
            'created_by' => auth()->user()->emp_id,
            'price_type' => 'RATE',
            'company_id' => auth()->user()->branch->company_id,
        ]);

        session()->flash('success', 'New cost added successfully.');
        $this->fetchData(); // Refresh data after adding new cost


        $this->menu_cost_amount = 0;
    }
}
