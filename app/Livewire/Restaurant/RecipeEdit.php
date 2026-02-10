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
use Livewire\WithFileUploads;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class RecipeEdit extends Component
{
    use WithFileUploads;
    public $recipeId;
    public $menu;
    public $recipes;
    public $items = [];
    public $categories = [];
    public $approvers = [];
    public $reviewers = [];
    public $hasReviewer = false;
    public $imagePath;
    public $menu_image;
    public $hasNewImage = false;

    public  $menu_name;
    public  $menu_code;
    public  $menu_type;
    public  $category_id;
    public  $approver;
    public  $reviewer;
    public $description;

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
         $this->menu_name = $this->menu->menu_name;
         $this->menu_code = $this->menu->menu_code;
         $this->menu_type = $this->menu->menu_type;
         $this->category_id = $this->menu->category_id;
         $this->approver = $this->menu->approver_id;
         $this->reviewer = $this->menu->reviewer_id;
         $this->description = $this->menu->menu_description;

        $this->recipes = Recipe::where('menu_id',$this->menu->id)->get();
        $this->imagePath = storage_path('app/public/' . $this->menu->menu_image);
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

    public function updatedMenuImage()
    {
        $this->validate([
            'menu_image' => 'image|max:2048', // Validate that the uploaded file is an image and its size does not exceed 2MB
        ]);
        $this->hasNewImage = true;
    }


    public function updateRecipe()
    {
        $this->validate([
            'menu_name' => 'required|string|max:255',
            'menu_code' => 'required|string|max:100|unique:menus,menu_code,' . $this->menu->id,
            'menu_type' => 'required|string|in:Ala Carte,Banquet',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'reviewer' => $this->hasReviewer ? 'required|exists:signatories,id' : 'nullable',
            'menu_image' => 'nullable|image|max:2048', // Validate that the uploaded file is an image and its size does not exceed 2MB
        ]);

        if ($this->hasNewImage) {
            if (!empty($this->menu->menu_image)) {
                Storage::disk('public')->delete($this->menu->menu_image);
            }
            $imagePath = $this->menu_image->store('recipe_images', 'public');
            $this->menu->menu_image = $imagePath;
        }
        $this->menu->menu_name = $this->menu_name;
        $this->menu->menu_code = $this->menu_code;
        $this->menu->menu_type = $this->menu_type;
        $this->menu->category_id = $this->category_id;
        $this->menu->approver_id = $this->approver;
        $this->menu->reviewer_id = $this->hasReviewer ? $this->reviewer : null;
        $this->menu->menu_description = $this->description;




        $this->menu->save();

        // Update or create recipes based on the input data
        // foreach ($this->recipes as $recipe) {
        //     $recipe->update([
        //         'item_id' => $recipe->item_id,
        //         'qty' => $recipe->quantity,
        //         'uom_id' => $recipe->uom_id,
        //         // Add other fields as necessary
        //     ]);
        // }

        session()->flash('success', 'Recipe updated successfully!');
    }

    public function render()
    {
        return view('livewire.restaurant.recipe-edit');
    }
}
