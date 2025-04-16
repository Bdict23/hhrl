<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
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
    }

    public function addCost()
    {
        $this->showForm = true;
        $this->dispatch('open-modal', 'add-cost-modal');
    }

    public function saveCost()
    {
        $this->validate([
            'newCost' => 'required|numeric|min:0',
            'supplierId' => 'required|exists:suppliers,id',
        ]);

        try {
            PriceLevel::create([
                'item_id' => $this->selectedItemId,
                'price_type' => 'COST',
                'amount' => $this->newCost,
                'supplier_id' => $this->supplierId,
                'created_at' => $this->costDate ?? now(),
            ]);

            session()->flash('success', 'Cost added successfully.');

            $this->dispatch('close-modal');

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
        $priceLevels = PriceLevel::with(['item', 'supplier'])
            ->whereHas('item', fn ($query) => $query->where('item_description', 'like', '%' . $this->search . '%'))
            ->where('price_type', 'COST')
            ->whereIn('id', fn ($subquery) => $subquery->selectRaw('MAX(id)')
                ->from('price_levels')
                ->groupBy('item_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(fn ($level) => [
                'id' => $level->id,
                'item_id' => $level->item_id,
                'cost' => $level->amount,
                'timestamp' => $level->created_at->format('Y-m-d'),
                'item_name' => $level->item?->item_description ?? 'N/A',
                'supplier_name' => $level->supplier?->supp_name ?? 'N/A',
            ]);

        $suppliers = Supplier::orderBy('supp_name')->get(['id', 'supp_name']);

        return view('livewire.item-cost', [
            'priceLevels' => $priceLevels,
            'months' => collect(range(1, 12))->mapWithKeys(fn ($m) => [
                $m => Carbon::create()->month($m)->format('F'),
            ]),
            'suppliers' => $suppliers,
        ]);

    }
}
