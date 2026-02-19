<?php

namespace App\Livewire\Restaurant;
use App\Models\Menu; // Assuming Menu model exists in App\Models namespace
use App\Models\Recipe; // Assuming Recipe model exists in App\Models namespace
use App\Models\Item;
use App\Models\UOM;
use App\Models\PriceLevel;
use App\Models\UnitConversion;
use App\Models\Module;
use App\Models\ModulePermission;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Cardex;
use Livewire\Attributes\Computed;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderMenu;
use App\Models\ProductionOrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ProductionOrderCreate extends Component
{
    //for mount
    public $items;
    public $recipes;
    public $orderStats;

    // for storing selected recipe and items and displaying in the production order form
    public $selectedRecipes;
    public $recipeIngredients;
    public $selectedItems;

    // calculation properties
     public $cardex = [];
     public $selectedRecipeIdTab;
     public $overallIngredients;


    // for production order form
    public $saveAs;
    public $prepared_by;
    public $notes;
    public $reference;

    public function render()
    {
        return view('livewire.restaurant.production-order-create');
    }

    public function mount(Request $request = null)
    {
        if ($request && $request->has('order_id')) {
            $orderId = $request->query('order_id');
            $this->loadData();
            $existingOrder = ProductionOrder::where('id', $orderId)->first();

            if ($existingOrder) {
                $this->loadExistingData($existingOrder->id);
            } else {
                $this->dispatch('showAlert', ['type' => 'error', 'message' => 'No existing production order found for the selected menu.']);
                $this->loadData();
            }
        }else {
            $this->loadData();
        }
    }

    public function loadExistingData($productionOrderId)
    {
        $productionOrder = ProductionOrder::with('employee', 'productionMenus', 'productionOrderDetails')->find($productionOrderId);

        if (!$productionOrder) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Production Order not found.']);
            return;
        }

        $this->prepared_by = $productionOrder->employee->name . ' ' . $productionOrder->employee->last_name;
        $this->notes = $productionOrder->notes;
        $this->saveAs = $productionOrder->status == 'DRAFT' ? 'DRAFT' : 'FINAL';
        $this->orderStats = $this->saveAs;
        $this->reference = $productionOrder->reference;
        $this->selectedRecipeIdTab = $productionOrder->productionMenus->first()->menu_id ?? null; // Set first menu as active tab
        // Load selected recipes and their ingredients
        foreach ($productionOrder->productionMenus as $productionMenu) {
            $menu = $productionMenu->menu;
            $this->selectedRecipes[] = [
                'id' => $menu->id,
                'menu_name' => $menu->menu_name,
                'qty_requested' => $productionMenu->qty,
                'recipe_id' => $menu->id,
            ];

            foreach ($menu->recipes as $recipe) {
                $cardexItem = collect($this->cardex)->firstWhere('item_code', $recipe->item->item_code);
                $this->recipeIngredients[] = [
                    'item_id' => $recipe->item_id,
                    'item' => $recipe->item->toArray(),
                    'qty' => $recipe->qty * ($productionMenu->qty ?? 1), // Multiply by requested qty
                    'base_qty' => $recipe->qty, // Store base qty for calculations
                    'recipe_id' => $menu->id,
                    'balance' => $cardexItem['total_balance'] ?? 0,
                    'total_available' => $cardexItem['total_available'] ?? 0,
                    'conversion_factor' => $recipe->conversionFactor(),
                    'uom' => ['unit_symbol' => $recipe->uom()->first()->unit_symbol ?? 'N/A'],
                    'uom_id' => $recipe->uom_id,
                    'base_uom' => $recipe->item->uom->unit_symbol,
                ];
            }
        }
    }

    public function loadData(){
        $this->prepared_by = auth()->user()->employee->name . ' '. auth()->user()->employee->last_name;
        $this->items = Item::with('priceLevel', 'units') // Added unitOfMeasures here
            ->where('item_status', 'ACTIVE')
            ->where('company_id', auth()->user()->branch->company_id)
            ->get();
        $this->cardex = $this->items->map(function ($item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $item->total_balance = $totalIn - $totalOut;
            $item->total_reserved = $totalReserved;
            $item->total_available = $item->total_balance - $totalReserved;

            return $item->toArray();
        });


        $this->recipes = Menu::with('categories')
            ->where('company_id', auth()->user()->branch->company_id)
            ->where('status', 'AVAILABLE')
            ->get();
    }

    public function addRecipe($recipeId)
    {
        $this->selectedRecipeIdTab = $recipeId;

        // 1. Check if recipe is already added
        if(collect($this->selectedRecipes)->contains('id', $recipeId)) {
            $this->dispatch('showAlert', ['type' => 'warning', 'message' => 'Recipe already added.']);
            return;
        }

        $selected = Menu::with('recipes','recipes.item','recipes.uom','recipes.item.uom')->find($recipeId);
        
        // 2. Add to selectedRecipes
        $this->selectedRecipes[] = array_merge($selected->toArray(), [
            'qty_requested' => 1,
            'recipe_id' => $selected->id
        ]);

        // 3. Add ingredients to recipeIngredients (Directly, no grouping here)
        foreach($selected->recipes as $ingredient) {
            $cardexItem = collect($this->cardex)->firstWhere('item_code', $ingredient->item->item_code);
            
            $this->recipeIngredients[] = array_merge($ingredient->toArray(), [
                'qty' => $ingredient->qty,
                'base_qty' => $ingredient->qty, // Base quantity for calculations when qty_requested changes
                'recipe_id' => $selected->id,
                'balance' => $cardexItem['total_balance'] ?? 0,
                'total_available' => $cardexItem['total_available'] ?? 0, // I-save ni para sa computation later
                'conversion_factor' => $ingredient->conversionFactor(),
            ]);
        }
    }

       #[Computed]
    public function overall()
    {
        return collect($this->recipeIngredients)
            ->groupBy('item_id')
            ->map(function ($group) {
                $first = $group->first();
                
                // Gamit og (float) casting ug i-ensure nga naay value
                $totalQty = $group->sum(function($ing) {
                    return is_numeric($ing['qty']) ? (float)$ing['qty'] : 0;
                });
                
                $factor = (float)($first['conversion_factor'] ?? 1);
                $totalAvailableBase = (float)($first['total_available'] ?? 0);
                
                // Project availability calculation
                $projectedAvailable = ($totalAvailableBase * $factor) - $totalQty;

                return [
                    'item_id'          => $first['item_id'],
                    'item_code'        => $first['item']['item_code'],
                    'item_description' => $first['item']['item_description'],
                    'qty'              => $totalQty,
                    'balance'          => $first['balance'],
                    'available'        => $projectedAvailable,
                    'base_uom'        => $first['item']['uom']['unit_symbol'] ?? 'N/A',
                    'uom'              => $first['uom']['unit_symbol'] ?? 'N/A',
                ];
            })->keyBy('item_id');
    }

    public function updated($propertyName)
    {
        // empty return if the qty_requested is empty or less than 1 to prevent errors in calculations
        if (str_contains($propertyName, 'selectedRecipes') && str_contains($propertyName, 'qty_requested')) {
            $parts = explode('.', $propertyName);
            $index = $parts[1];
            $recipe = $this->selectedRecipes[$index];
            if (empty($recipe['qty_requested']) || $recipe['qty_requested'] < 1) {
                return;
            }
        }
        

        // Check kung ang gi-update kay ang qty_requested sa selectedRecipes array
        // Sample property name: selectedRecipes.0.qty_requested
        if (str_contains($propertyName, 'selectedRecipes') && str_contains($propertyName, 'qty_requested')) {
            
            // Kuhaon nato ang index gikan sa string
            $parts = explode('.', $propertyName);
            $index = $parts[1];

            $recipe = $this->selectedRecipes[$index];
            $newQty = $recipe['qty_requested'] ?? 1;

            // I-update tanan ingredients nga sakop niini nga recipe
            foreach ($this->recipeIngredients as $key => $ingredient) {
                if ($ingredient['recipe_id'] == $recipe['id']) {
                    // Formula: Original Ingredient Qty * New Recipe Quantity
                    // Note: Kinahanglan ang imong 'qty' sa database mao ang base (for 1 serving)
                    $baseQty = $ingredient['base_qty'] ?? $ingredient['qty']; 
                    $this->recipeIngredients[$key]['qty'] = $baseQty * $newQty;
                }
            }
        }
    }
    public function updatedRecipeIngredients($value, $key)
    {
        // Kon ang gi-update mao ang 'qty' ug kini nahimong empty string
        if (str_ends_with($key, '.qty') && ($value === '' || $value === null)) {
            // I-set ang value ngadto sa 0
            data_set($this->recipeIngredients, $key, 0);
        }
    }

        // 1. Tangtangon ang tibuok Recipe (apil tanan ingredients ubos niini)
    public function removeRecipe($recipeId)
    {
        // Tangtangon ang recipe gikan sa napili nga recipes
        $this->selectedRecipes = collect($this->selectedRecipes)
            ->reject(fn($recipe) => $recipe['id'] == $recipeId)
            ->toArray();

        // Tangtangon tanang ingredients nga konektado niini nga recipe
        $this->recipeIngredients = collect($this->recipeIngredients)
            ->reject(fn($ing) => $ing['recipe_id'] == $recipeId)
            ->toArray();

        // Optional: I-reset ang tab view kung ang gi-delete mao ang gi-view karon
        if ($this->selectedRecipeIdTab == $recipeId) {
            $this->selectedRecipeIdTab = collect($this->selectedRecipes)->first()['id'] ?? null;
        }

        $this->dispatch('showAlert', ['type' => 'info', 'message' => 'Recipe removed.']);
    }

    // 2. Tangtangon ang specific nga ingredient item lang sulod sa usa ka recipe
    public function removeIngredientItem($itemId, $recipeId = null)
    {
        $this->recipeIngredients = collect($this->recipeIngredients)
            ->reject(function ($ing) use ($itemId, $recipeId) {
                // Kung manual item (null recipeId), match lang ang itemId
                // Kung recipe item, kinahanglan match ang itemId ug recipeId
                return $ing['item_id'] == $itemId && $ing['recipe_id'] == $recipeId;
            })->toArray();
        $this->dispatch('showAlert', ['type' => 'info', 'message' => 'Ingredient item removed.']);
    }

    public function removeOverallIngredientItem($itemId)
    {
        $this->recipeIngredients = collect($this->recipeIngredients)
            ->reject(fn($ing) => $ing['item_id'] == $itemId)
            ->toArray();
        $this->dispatch('showAlert', ['type' => 'info', 'message' => 'Ingredient item removed from overall list.']);
    }

        // Function sa pag-add og manual item (walay recipe)
    public function addItem($itemId)
    {
        $item = Item::with('uom')->find($itemId);
        $cardexItem = collect($this->cardex)->firstWhere('item_code', $item->item_code);

        // Check kung naga-exist na ba ang manual entry ani nga item
        $existingIndex = collect($this->recipeIngredients)->search(function($ing) use ($itemId) {
            return $ing['item_id'] == $itemId && $ing['recipe_id'] === null;
        });

        if ($existingIndex !== false) {
            $this->recipeIngredients[$existingIndex]['qty'] += 1;
        } else {
            $this->recipeIngredients[] = [
                'item_id'           => $item->id,
                'item'              => $item->toArray(),
                'qty'               => 1,
                'base_qty'          => 1,
                'recipe_id'         => null, // Timailhan nga manual add ni
                'balance'           => $cardexItem['total_balance'] ?? 0,
                'total_available'   => $cardexItem['total_available'] ?? 0,
                'conversion_factor' => 1,
                'uom'               => ['unit_symbol' => $item->uom->unit_symbol ?? 'N/A'],
            ];
        }
    }


    public function productonOrderSave(){
         $this->validate([
            "saveAs" => "required|in:DRAFT,FINAL,",
            "notes" => "nullable|string",
            "selectedRecipes" => "required|array|min:1",
            "selectedRecipes.*.id" => "required|exists:menus,id",
            "recipeIngredients" => "required|array|min:1",
            "recipeIngredients.*.item_id" => "required|exists:items,id",
            "recipeIngredients.*.qty" => "required|numeric|min:0.01",
         ]);

            $curYear = now()->year;
            $branchId = auth()->user()->branch_id;
            $yearlyCount = ProductionOrder::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;

            // Save logic here (e.g., create ProductionOrder and related details)
            $productionOrder = new ProductionOrder();
            $productionOrder->reference = 'PRD-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);;
            $productionOrder->branch_id = auth()->user()->branch_id;
            $productionOrder->status = $this->saveAs == "FINAL" ? "PENDING" : "DRAFT";
            $productionOrder->notes = $this->notes;
            $productionOrder->prepared_by = auth()->user()->employee->id;
            $productionOrder->created_at = Carbon::now('Asia/Manila');
            $productionOrder->updated_at = Carbon::now('Asia/Manila');
            $productionOrder->save();

            foreach($this->selectedRecipes as $selectedRecipe){
                ProductionOrderMenu::create([
                    'branch_id' => auth()->user()->branch_id,
                    'production_order_id' => $productionOrder->id,
                    'menu_id' => $selectedRecipe['id'],
                    'qty' => $selectedRecipe['qty_requested'],
                    'created_at' => Carbon::now('Asia/Manila'),
                    'updated_at' => Carbon::now('Asia/Manila'),
                ]);

            }

            foreach($this->recipeIngredients as $ingredient){
                ProductionOrderDetail::create([
                    'branch_id' => auth()->user()->branch_id,
                    'production_order_id' => $productionOrder->id,
                    'item_id' => $ingredient['item_id'],
                    'menu_id' => $ingredient['recipe_id'],
                    'qty' => $ingredient['qty'],
                    'uom_id' => $ingredient['uom']['id'] ?? $ingredient['item']['uom']['id'],
                    'created_at' => Carbon::now('Asia/Manila'),
                    'updated_at' => Carbon::now('Asia/Manila'),
                ]);

                $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Production Order saved successfully.']);
            }
            
            




    }

}