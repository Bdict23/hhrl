<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;
use App\Models\PriceLevel;
use App\Models\Supplier;
use Illuminate\Support\Carbon;

class ItemCost extends Component
{
    use WithPagination;

    public $search = '';
    public $chartData = [];
    public $chartYear;
    public $chartMonth;
    public $selectedItemId;
    public $selectedItemName;
    public $availableYears = [];
    public $chartLoading = false;
    public $newCost;
    public $supplierId;
    public $costDate;
    public $showForm = false;

    public function mount()
    {
        $this->availableYears = PriceLevel::query()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        $this->supplierId = '';
    }

    protected function rules()
    {
        return [
            'chartYear' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'chartMonth' => 'nullable|integer|min:1|max:12',
            'newCost' => 'required|numeric|min:0',
            'supplierId' => 'required|exists:suppliers,id',
            'selectedItemId' => 'required|integer|exists:items,id',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function showChart($itemId)
    {
        $this->validateOnly('chartYear');
        $this->validateOnly('chartMonth');
        $this->selectedItemId = $itemId;
        $this->chartLoading = true;

        try {
            $query = PriceLevel::where('item_id', $itemId)
                ->when($this->chartYear, fn ($q) => $q->whereYear('created_at', $this->chartYear))
                ->when($this->chartMonth, fn ($q) => $q->whereMonth('created_at', $this->chartMonth))
                ->orderBy('created_at');

            if ($query->count() === 0) {
                $this->addError('chart', 'No cost data available for this item.');
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

    public function setItem($itemId, $itemName)
    {
        $this->selectedItemId = $itemId;
        $this->selectedItemName = $itemName;
        $this->newCost = null;
        $this->supplierId = '';
        $this->costDate = now()->format('Y-m-d');
        $this->resetErrorBag();
        $this->showForm = true; // Set showForm here to ensure state is ready
        $this->dispatch('openAddCostModal', itemId: $itemId, itemName: $itemName); // Dispatch with parameters for debugging
    }

    public function addCost()
    {
        // No need to set showForm here; handled in setItem
        // This method is kept for compatibility with existing Blade events
    }

    public function saveCost()
    {
        $this->validate([
            'newCost' => 'required|numeric|min:0',
            'supplierId' => 'required|exists:suppliers,id',
            'selectedItemId' => 'required|integer|exists:items,id',
        ]);

        try {
            PriceLevel::create([
                'item_id' => $this->selectedItemId,
                'price_type' => 'COST',
                'amount' => $this->newCost,
                'supplier_id' => $this->supplierId,
                'created_at' => now(),
                'company_id' => auth()->user()->branch->company_id,
                'branch_id' => auth()->user()->branch_id,
            ]);

            session()->flash('success', 'Cost added successfully for ' . $this->selectedItemName);

            $this->dispatch('closeAddCostModal');

            if ($this->selectedItemId) {
                $this->showChart($this->selectedItemId);
            }
            $this->showForm = false;
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving cost: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset(['newCost', 'selectedItemId', 'selectedItemName', 'showForm', 'supplierId', 'costDate']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $items = Item::query()
            ->where('company_id', auth()->user()->branch->company_id)
            ->where('item_description', 'like', '%' . $this->search . '%')
            ->with(['priceLevels' => function ($query) {
                $query->where('price_type', 'COST')
                      ->orderBy('created_at', 'desc')
                      ->take(1)
                      ->with('supplier');
            }])
            ->orderBy('item_description', 'asc')
            ->paginate(10)
            ->through(function ($item) {
                $priceLevel = $item->priceLevels->first();
                return [
                    'id' => $item->id,
                    'item_id' => $item->id,
                    'item_name' => $item->item_description ?? 'N/A',
                    'cost' => $priceLevel ? (float) $priceLevel->amount : null,
                    'timestamp' => $priceLevel ? Carbon::parse($priceLevel->created_at)->format('Y-m-d') : 'N/A',
                    'supplier_name' => $priceLevel && $priceLevel->supplier ? $priceLevel->supplier->supp_name : 'N/A',
                ];
            });

        // modified by bdict
        $suppliers = Supplier::orderBy('supp_name')->where([['supplier_status','active'],['company_id', auth()->user()->branch->company_id]])->get(['id', 'supp_name']);

        return view('livewire.item-cost', [
            'priceLevels' => $items,
            'months' => collect(range(1, 12))->mapWithKeys(fn ($m) => [
                $m => Carbon::create()->month($m)->format('F'),
            ]),
            'suppliers' => $suppliers,
        ]);
    }
}