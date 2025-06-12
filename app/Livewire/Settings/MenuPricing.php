<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\Menu;

class MenuPricing extends Component
{
    public $categories = [];
    public $selectedCategory = null;
    public $menus = [];
    public $recipestWithTotalCost = [];
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
        $this->menus = Menu::with(['recipes','recipes.latestItemCost'])->where('status', 'available')->get();
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
}
