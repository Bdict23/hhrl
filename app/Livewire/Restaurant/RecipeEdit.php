<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\Menu; // Assuming Menu model exists in App\Models namespace
use App\Models\Recipe; // Assuming Recipe model exists in App\Models namespace
use App\Models\Item;
use App\Models\UOM;
use App\Models\PriceLevel;
use App\Models\UnitConversion;
use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Employee;
use App\Models\Signatory;
use App\Models\Category;


use Illuminate\Http\Request;


class RecipeEdit extends Component
{
    public $recipeId;
    public $menu;
    public $recipes;
    public $items = [];
    public $categories = [];
    public $approvers = [];
    public $reviewers = [];
    public $hasReviewer = false;

    public  function mount(Request $request)
    {
       

      if(auth()->user()->employee->getModulePermission('Restaurant - Edit Recipe') != 2 ){
            if($request->has('recipe-id')) {
                
            $this->recipeId = $request->input('recipe-id');
            $this->fetchData();

        } else {
            // Handle the case when 'recipe-id' is not provided in the request
            // For example, you can redirect back or show an error message
            return redirect()->back()->with('error', 'Recipe ID is required.');
        }
        }else{
            return redirect()->to('dashboard');
        }
        
        
    }

    public function fetchData()
    {
         $this->menu = Menu::find($this->recipeId);
        $this->recipes = Recipe::where('menu_id',$this->menu->id)->get();
        $this->hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Recipe') == 1 ? true : false;
        $this->items = Item::with('priceLevel', 'units') // Added unitOfMeasures here
            ->where('item_status', 'ACTIVE')
            ->where('company_id', auth()->user()->branch->company_id)
            ->get();
        $module = Module::where('module_name', 'Recipe')->first();
        $this->categories = Category::where([['status', 'ACTIVE'], ['company_id', auth()->user()->branch->company_id], ['category_type', 'MENU']])->get();
        $this->approvers = Signatory::where([['signatory_type', 'APPROVER'], ['status', 'ACTIVE'], ['MODULE_ID', $module->id ], ['branch_id', auth()->user()->branch_id]])->get();
        $this->reviewers = Signatory::where([['signatory_type', 'REVIEWER'], ['status', 'ACTIVE'], ['MODULE_ID', $module->id], ['branch_id', auth()->user()->branch_id]])->get();
        
    }

    public function render()
    {
        return view('livewire.restaurant.recipe-edit');
    }
}
