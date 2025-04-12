<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PriceLevel;
use Illuminate\Support\Carbon;

class ItemCost extends Component
{
    use WithPagination;

    public $search = '';
    public $chartYear;
    public $chartMonth;
    public $selectedItemId;
    public $availableYears;
    public $chartData = [];
    public $chartLoading = false;

    public function mount()
    {
        $this->availableYears = PriceLevel::query()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }

    public function rules()
    {
        return [
            'chartYear' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'chartMonth' => 'nullable|integer|min:1|max:12',
        ];
    }

    public function showChart($itemId)
    {
        $this->validate();
        $this->selectedItemId = $itemId;
        $this->chartLoading = true;

        try {
            $query = PriceLevel::where('item_id', $itemId)
                ->when($this->chartYear, function ($q) {
                    $q->whereYear('created_at', $this->chartYear);
                })
                ->when($this->chartMonth, function ($q) {
                    $q->whereMonth('created_at', $this->chartMonth);
                });

            if ($query->count() > 5000) {
                $this->addError('chart', 'Too many records to display. Please narrow your filters.');
                return;
            }

            $this->chartData = $query
                ->orderBy('created_at')
                ->get()
                ->map(function ($level) {
                    return [
                        'date' => $level->created_at->format('Y-m-d'),
                        'cost' => (float)$level->amount,
                        'supplier' => $level->supplier?->supp_name ?? 'N/A',
                    ];
                })
                ->toArray();

            $this->dispatch('loadAndRenderChart', $this->chartData);

        } catch (\Exception $e) {
            $this->addError('chart', 'Error loading chart data: ' . $e->getMessage());
        } finally {
            $this->chartLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.item-cost', [
            'priceLevels' => PriceLevel::with(['item', 'supplier'])
                ->whereHas('item', function ($query) {
                    $query->when($this->search, function ($q) {
                        $q->where('item_description', 'like', '%' . $this->search . '%');
                    });
                })
                ->where('price_type', 'COST')
                ->whereIn('id', function ($subquery) {
                    $subquery->selectRaw('MAX(id)')
                        ->from('price_levels')
                        ->groupBy('item_id');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'months' => collect(range(1, 12))->mapWithKeys(fn ($m) => [
                $m => Carbon::create()->month($m)->format('F')
            ])
        ]);
    }
    public function render()
    {
        
        $priceLevels = PriceLevel::with(['item', 'supplier'])
            ->whereHas('item', function ($query) {
                $query->where('item_description', 'like', '%' . $this->search . '%');
            })
            ->where('price_type', 'COST')
            ->whereIn('id', function ($subquery) {
                $subquery->selectRaw('MAX(id)')
                    ->from('price_levels')
                    ->groupBy('item_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($level) {
                return [
                    'id'          => $level->id,
                    'item_id'     => $level->item_id,
                    'cost'        => $level->amount,
                    'timestamp'   => $level->created_at->format('Y-m-d'),
                    'item_name'   => $level->item?->item_description ?? 'N/A',
                    'supplier_name' => $level->supplier?->supp_name ?? 'N/A',
                ];
            });

        return view('livewire.item-cost', [
            'priceLevels' => $priceLevels,
            'months' => collect(range(1, 12))->mapWithKeys(fn ($m) => [
                $m => Carbon::create()->month($m)->format('F')
            ])
            
        ]);
    }

}