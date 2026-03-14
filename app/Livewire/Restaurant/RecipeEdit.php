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
    public $recipesArray = [];
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
    public $status;
    public $hasReviewerConfig;

    public  function mount(Request $request)
    {
        $this->hasReviewerConfig = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Recipe') == 1 ? true : false;

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
         $this->status = $this->menu->status;
        $recipes = Recipe::where('menu_id',$this->menu->id)->get();
        foreach($recipes as $index => $recipe){
            $this->recipesArray[$index]['item_id'] = $recipe->item_id;
            $this->recipesArray[$index]['qty'] = number_format($recipe->qty,0,0);
            $this->recipesArray[$index]['uom_id'] = $recipe->uom_id;
            $this->recipesArray[$index]['price_level_id'] = $recipe->price_level_id;
                $this->recipesArray[$index]['item'] = $recipe->item;
                $this->recipesArray[$index]['uom'] = $recipe->uom;
                $this->recipesArray[$index]['price_level'] = $recipe->price_level;
                $this->recipesArray[$index]['latestItemCost'] = ($recipe->item->costPrice->amount ?? 0);
                $this->recipesArray[$index]['conversionFactor'] = $recipe->conversionFactor();
        }
        $this->imagePath = storage_path('app/public/' . $this->menu->menu_image);
        $this->hasReviewerConfig = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Recipe') == 1 ? true : false;
        $this->items = Item::with('priceLevel', 'units', 'subUnits') // Added unitOfMeasures here
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


    public function updateAction(){
        if($this->status == 'AVAILABLE'){ 
            if($this->hasReviewerConfig){
                $this->dispatch('confirmation', ['type' => 'warning', 'title' => 'Warning', 'message' => 'Updating an available recipe will set its status back to (FOR REVIEW) and the reviewer will be notified to review the changes.']);
            }else{
             $this->dispatch('confirmation', ['type' => 'warning', 'title' => 'Warning', 'message' => 'Updating an available recipe will set its status back to (FOR APPROVAL) and the approver will be notified to review the changes.']);
            }
        } else {
             $this->dispatch('confirmation', ['type' => 'info', 'title' => 'Info', 'message' => 'The recipe will be updated with the current changes.']);
        }
    }

    public function updateRecipe()
    {
        $this->validate([
            'menu_name' => 'required|string|max:255',
            'menu_code' => 'required|string|max:100|unique:menus,menu_code,' . $this->menu->id,
            'menu_type' => 'required|string|in:Ala Carte,Banquet',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'reviewer' => $this->hasReviewerConfig ? 'required|exists:employees,id' : 'nullable',
            'menu_image' => 'nullable|image|max:2048', // Validate that the uploaded file is an image and its size does not exceed 2MB
            'approver' => 'required|exists:employees,id',
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
        $this->menu->reviewer_id = $this->hasReviewerConfig ? $this->reviewer : null;
        $this->menu->menu_description = $this->description;
        if($this->status == 'AVAILABLE'){
            $this->menu->status = $this->hasReviewerConfig ? 'FOR REVIEW' : 'FOR APPROVAL';
        }

        $this->menu->save();

        // delete existing recipes
        Recipe::where('menu_id', $this->menu->id)->delete();
        // create new recipes
        foreach ($this->recipesArray as $recipe) {
            Recipe::create([
                'menu_id' => $this->menu->id,
                'item_id' => $recipe['item_id'],
                'qty' => ($recipe['qty']),
                'uom_id' => $recipe['uom_id'],
                'price_level_id' => $recipe['price_level_id'],
            ]);
        }

        $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Recipe updated successfully!']);
    }

    public function render()
    {
        return view('livewire.restaurant.recipe-edit');
    }

    public function removeRecipe($index)
    {
        unset($this->recipesArray[$index]);
        $this->recipesArray = array_values($this->recipesArray);

    }

    public function appendToRecipe( $itemId, $uomId , $priceLevelId, $factor)
    {
        
        $item = Item::find($itemId);
        $uom = UOM::find($uomId);
        $priceLevel = PriceLevel::find($priceLevelId);
        if (!$item || !$uom || !$priceLevel) {
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'Invalid item, Unit, or Cost price.']);
            return;
        }
        if(!$item->costPrice || $item->costPrice->amount == null || $item->costPrice->amount == 0){
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'The selected item does not have a cost price. Please update the item cost price before adding it to the recipe.']);
            return;
        }
         foreach($this->recipesArray as $recipe){
            if($recipe['item_id'] == $itemId){
                $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'The item already exists in the recipe.']);
                return;
            }
        }
        $price = round((1 / $factor) * $item->costPrice->amount * 100) / 100;

        $this->recipesArray[] = [
            'item_id' => $itemId,
            'qty' => 1,
            'uom_id' => $uomId,
            'price_level_id' => $priceLevelId,
            'item' => $item,
            'uom' => $uom,
            'price_level' => $priceLevel,
            'latestItemCost' => $price,
            'conversionFactor' => $factor, 
        ];

        $this->dispatch('recipe-added');
        
    }

    public function selectedUom($factor, $itemId){
        foreach($this->recipesArray as $index => $recipe){
            if($recipe['item_id'] == $itemId){
                $this->recipesArray[$index]['conversionFactor'] = $factor;
                break;
            }
        }
        
    }
}
