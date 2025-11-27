<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\BranchMenuRecipe;
use App\Models\Menu;
use App\Models\BranchMenu;

class DailyRecipeCount extends Component
{
    public $branchMenuRecipes;
    public $menus;
    public $branchMenus;
    public function mount()
    {
        $currentWeekday = date('D'); // Get the current day of the week (Mon, Tue, Wed, etc.)
        $columnName = strtolower($currentWeekday);
        $this->branchMenuRecipes = BranchMenuRecipe::with('menu', 'branchMenu')
            ->whereHas('branchMenu', function ($query) use ($columnName) {
                $query->where('branch_id', auth()->user()->branch_id)->where('is_available', '1')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where($columnName, '1');
            })
            ->get();
          

        $this->menus = Menu::whereIn('id', $this->branchMenuRecipes->pluck('menu_id'))->get();
        $this->branchMenus = BranchMenu::where('branch_id', auth()->user()->branch_id)->get();
    }
    public function render()
    {
        return view('livewire.restaurant.daily-recipe-count');
    }
}
