<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\BranchMenuRecipe;
use App\Models\Menu;
use Carbon\Carbon;
use App\Models\BranchMenu;
use App\Models\RecipeCardex;
use App\Models\InventoryAdjustment;

class DailyRecipeCount extends Component
{
    public $branchMenuRecipes;
    public $menus;
    public $branchMenus;
    public $selectedRecipeId;
    public $availableQty;
    public $recipes = [];
    public $cardexDetails;
    public $additionalQTY;

    public function mount()
    { 
        $this->loadData();
    }
    public function loadData()
    {
        $currentWeekday = date('D'); // Get the current day of the week (Mon, Tue, Wed, etc.)
        $columnName = strtolower($currentWeekday);
        $AlaCarteMenus  = Menu::where('recipe_type', 'Ala carte')->pluck('id')->toArray();
        $this->branchMenuRecipes = BranchMenuRecipe::with('menu', 'branchMenu')
            ->whereHas('branchMenu', function ($query) use ($columnName) {
                $query->where('branch_id', auth()->user()->branch_id)->where('is_available', '1')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where($columnName, '1');
            })->whereIn('menu_id', $AlaCarteMenus)
            ->get();
          

        $this->menus = Menu::whereIn('id', $this->branchMenuRecipes->pluck('menu_id'))->get();
        $this->recipes = Menu::where('company_id', auth()->user()->company_id)->where('status','AVAILABLE')->get();
        $this->branchMenus = BranchMenu::where('branch_id', auth()->user()->branch_id)->get();
    }
    public function render()
    {
        return view('livewire.restaurant.daily-recipe-count');
    }

    
    public function editRecipe($recipeId)
    {
        $this->selectedRecipeId = $recipeId;
    }

    public function updateQuantity()
    {
        $recipe = BranchMenuRecipe::find($this->selectedRecipeId);
        if ($recipe) {

        $recipe->bal_qty = ( $recipe->bal_qty += $this->additionalQTY );
        $recipe->save();

        $currentYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = InventoryAdjustment::where('branch_id', $branchId)
            ->whereYear('created_at', $currentYear)
            ->count() + 1;

        $adjustment = new InventoryAdjustment();
        $adjustment->reference =  'ADJ-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        $adjustment->adjustment_type = 'INCREASE';
        $adjustment->branch_id = auth()->user()->branch_id;
        $adjustment->status = 'APPROVED';
        $adjustment->created_by = auth()->user()->emp_id;
        $adjustment->reason = 'INVENTORY_COUNT';
        $adjustment->approved_by = auth()->user()->emp_id;
        $adjustment->created_at = Carbon::now('Asia/Manila');
        $adjustment->updated_at = Carbon::now('Asia/Manila');
        $adjustment->save();

        $recipeCardex = new RecipeCardex();
        $recipeCardex->branch_id = auth()->user()->branch_id;
        $recipeCardex->menu_id = $recipe->menu_id;
        $recipeCardex->qty_in = $this->additionalQTY;
        $recipeCardex->status = 'FINAL';
        $recipeCardex->transaction_type = 'ADJUSTMENT';
        $recipeCardex->adjustment_id = $adjustment->id;
        $recipeCardex->final_date = Carbon::now('Asia/Manila');
        $recipeCardex->created_at = Carbon::now('Asia/Manila');
        $recipeCardex->updated_at = Carbon::now('Asia/Manila');
        $recipeCardex->save();

        $this->dispatch('success');
        $this->reset();
        $this->loadData(); // Refresh the data
        $this->dispatch('refresh');
        }
    }

    public function viewCardex($menuId)
    {
      
        $this->cardexDetails = RecipeCardex::where('branch_id', auth()->user()->branch_id)->where('menu_id', $menuId)->orderBy('created_at', 'desc')->with('adjustment', 'order.invoice')->get();
       
    }
}
