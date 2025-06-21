<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\BranchMenu;
use App\Models\BranchMenuRecipe;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Category; // Assuming you have a Category model for menu categories

class MyMenu extends Component
{
    public $tableId;
    public $menuCategories = []; // This can hold the categories for the selected table
    public $menuItems = []; // This can hold the menu items for the selected table
    public function render()
    {
        return view('livewire.restaurant.my-menu');
    }
    public function mount(Request $request = null)
    {
        if ($request->has('table-id')) {
            $this->tableId = $request->input('table-id');
            $this->fetchData();
        }
        // Initialization logic can go here if needed
    }
    public function fetchData()
    {
        $weekOfDay = strtolower(Carbon::now()->format('D'));
        $branchMenuAvailable = BranchMenu::where('branch_id', Auth::user()->branch->id)
            ->where('is_available', '1')->where($weekOfDay, '1')->where('start_date', '<=', Carbon::now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', Carbon::now());
            })
            ->pluck('id')
            ->toArray();
        $branchRecipeAvailable = BranchMenuRecipe::whereIn('branch_menu_id', $branchMenuAvailable)
            ->pluck('menu_id')
            ->toArray();
        $branchCategoryAvailable = BranchMenuRecipe::whereIn('branch_menu_id', $branchMenuAvailable)
            ->with('menu.categories')
            ->get()
            ->pluck('menu.categories')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();
        $this->menuCategories = Category::where('company_id', Auth::user()->branch->company_id)
            ->where('status', 'ACTIVE')
            ->where('category_type', 'MENU')
            ->whereIn('id', $branchCategoryAvailable)
            ->get();
        $this->menuItems = Menu::with('categories', 'price_levels', 'recipes')
            ->where('company_id', Auth::user()->branch->company_id)
            ->where('status', 'AVAILABLE')
            ->where('recipe_type', 'Ala Carte')
            ->whereIn('id', $branchRecipeAvailable)
            ->get();
        //    dd($menuItems);
    }

    public function selectedCategory($categoryId)
    { 
        if($categoryId == 'all') {
            // If no category is selected, fetch all items
            $this->fetchData();
            return;
        }
        $weekOfDay = strtolower(Carbon::now()->format('D'));
        $branchMenuAvailable = BranchMenu::where('branch_id', Auth::user()->branch->id)
            ->where('is_available', '1')->where($weekOfDay, '1')->where('start_date', '<=', Carbon::now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', Carbon::now());
            })
            ->pluck('id')
            ->toArray();
        $branchRecipeAvailable = BranchMenuRecipe::whereIn('branch_menu_id', $branchMenuAvailable)
            ->pluck('menu_id')
            ->toArray();
        // Filter menu items based on the selected category
        $this->menuItems = Menu::with('categories', 'price_levels', 'recipes')
            ->where('company_id', Auth::user()->branch->company_id)
            ->where('status', 'AVAILABLE')
            ->where('recipe_type', 'Ala Carte')
            ->where('category_id', $categoryId) // Filter by selected category
            ->whereIn('id', $branchRecipeAvailable)
            ->get();
    }
}
