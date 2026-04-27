<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\PriceLevel;
use App\Models\Menu;
use App\Models\Item;
use Carbon\Carbon;

class MenuPricing extends Component
{
    public $categories = [];
    public $selectedCategory = null;
    public $menus = [];
    public $recipestWithTotalCost = [];
    public $selectedMenuId = null;
    public $menu_cost_amount = 0;
    public $chartYear = null;
    public $chartMonth = null;
    public $chartData = [];
    public $chartLoading = false;
    public $availableYears = [];

    protected function rules()
    {
        return [
            'chartYear' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'chartMonth' => 'nullable|integer|min:1|max:12',
            'selectedMenuId' => 'required|integer|exists:menus,id',
        ];
    }
    public function render()
    {
         $items = Menu::query()
            ->where('company_id', auth()->user()->branch->company_id)
            ->with(['price_levels' => function ($query) {
                $query->where('price_type', 'RATE')
                      ->where('branch_id', auth()->user()->branch_id)
                      ->orderBy('created_at', 'desc')
                      ->take(1);
            }])
            ->orderBy('menu_name', 'asc')
            ->paginate(10)
            ->through(function ($item) {
                $priceLevel = $item->price_levels->first();
                return [
                    'id' => $item->id,
                    '' => $item->id,
                    'menu_name' => $item->menu_name ?? 'N/A',
                    'cost' => $priceLevel ? (float) $priceLevel->amount : null,
                    'timestamp' => $priceLevel ? Carbon::parse($priceLevel->created_at)->format('Y-m-d') : 'N/A',
                ];
            });

        return view('livewire.settings.menu-pricing', [
            'priceLevels' => $items,
            'months' => collect(range(1, 12))->mapWithKeys(fn ($m) => [
                $m => Carbon::create()->month($m)->format('F'),
            ]),
        ]);
    }

    public function mount()
    {
         $this->availableYears = PriceLevel::query()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        // $this->fetchData();
        // Initialization logic can go here if needed
    }
    public function fetchData()
    {
        $this->categories = Category::where('category_type', 'MENU')
            ->where('status', 'ACTIVE')
            ->get();
        $this->menus = Menu::with(['recipes','recipes.latestItemCost','mySRP'])->where('status', 'available')->where('company_id', auth()->user()->branch->company_id)->get();
        // dd($this->menus);
        $this->recipestWithTotalCost = $this->menus->map(function ($menu) {
            $recipes = $menu->recipes ?? collect(); // Ensure recipes is a collection
            $totalCost = $recipes->sum(function ($recipe) {
                $itemCost = $recipe->latestItemCost?->amount ?? 0;
                $convertUnit = $recipe->conversionFactor();
                $itemCost =   $itemCost / $convertUnit ;
                // dd($recipe->conversionFactor(). '' . $recipe->item->item_description . '' .  $recipe->item->id );
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
            'amount' => $this->menu_cost_amount, // this supposed to be SRP
            'branch_id' => auth()->user()->branch_id,
            'created_by' => auth()->user()->emp_id,
            'price_type' => 'RATE',
            'company_id' => auth()->user()->branch->company_id,
        ]);

        // save menu new cost
        PriceLevel::create([
            'menu_id' => $this->selectedMenuId,
            'amount' => $this->recipestWithTotalCost->firstWhere('menu_id', $this->selectedMenuId)['total_cost'],
            'branch_id' => auth()->user()->branch_id,
            'created_by' => auth()->user()->emp_id,
            'price_type' => 'COST',
            'company_id' => auth()->user()->branch->company_id,
        ]);

        session()->flash('success', 'Selling Price updated successfully.');
        $this->fetchData(); // Refresh data after adding new cost


        $this->menu_cost_amount = 0;
    }

     public function viewPriceTrend($menuId)
    {
        $this->validateOnly('chartYear');
        $this->validateOnly('chartMonth');
        $this->selectedMenuId = $menuId;
        $this->chartLoading = true;

        try {
            $query = PriceLevel::where('menu_id', $menuId)
                ->when($this->chartYear, fn ($q) => $q->whereYear('created_at', $this->chartYear))
                ->when($this->chartMonth, fn ($q) => $q->whereMonth('created_at', $this->chartMonth))
                ->orderBy('created_at');

            if ($query->count() === 0) {
                $this->addError('chart', 'No cost data available for this menu.');
                $this->chartData = [];
                $this->dispatch('loadAndRenderChart', $this->chartData);
                return;
            }

            if ($query->count() > 5000) {
                $this->addError('chart', 'Too many records to display. Please narrow your filters.');
                return;
            }

            $this->chartData = $query->get()->map(fn ($level) => [
                'date' => $level->created_at->format('Y-m-d'),
                'cost' => (float) $level->amount,
                'supplier' => $level->supplier?->supp_name ?? 'N/A',
            ])->toArray();

            $this->dispatch('loadAndRenderChart', $this->chartData);
        } catch (\Exception $e) {
            $this->addError('chart', 'Error loading chart data: ' . $e->getMessage());
        } finally {
            $this->chartLoading = false;
        }
    }
}
