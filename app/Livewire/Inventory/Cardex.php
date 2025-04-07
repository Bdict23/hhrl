<?php

namespace App\Livewire\Inventory;

use Livewire\Component;

use App\Models\Location;
use App\Models\PriceLevel;
use App\Models\Cardex as CardexModel;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Cardex extends Component
{

    public $cardex = [];
    public $itemCode = null;
    public $itemDescription = null;
    public $location = null;
    public $price = null;
    public $totalIn = 0;
    public $totalOut = 0;
    public $totalBalance = 0;
    public $cardexData = [];
    public $itemId = null;


    protected $listeners = [
        'getCardexData' => 'getCardexData',
    ];
    protected $rules = [
        'itemCode' => 'string|exists:items,item_code',
    ];
    public function mount($itemCode = null)
    {
        $this->itemCode = $itemCode;
        if ($this->itemCode) {
            $this->getCardexData($this->itemCode);
        }
    }

    public function updatedItemCode($value)
    {
        if ($value) {
            $this->getCardexData($value);
        }
    }

    public function getCardexData($itemCode)
    {
        $item = Item::where('item_code', $itemCode)->first();
        if (!$item) {
            $this->reset(['itemDescription', 'location', 'price', 'totalBalance', 'cardex']);
            return;
        }

        $this->itemDescription = $item->item_description;
        $this->location = Location::where('item_id', $item->id)
            ->where('branch_id', auth()->user()->branch_id)
            ->value('location_name') ?? 'N/A';

        $this->price = PriceLevel::where('item_id', $item->id)
            ->where('branch_id', auth()->user()->branch_id)
            ->where('price_type', 'SRP')
            ->value('amount') ?? '0.00';

        $this->totalIn = CardexModel::where('item_id', $item->id)
            ->where('status', 'final')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->sum('qty_in');

        $this->totalOut = CardexModel::where('item_id', $item->id)
            ->where('status', 'final')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->sum('qty_out');

        $this->totalBalance = $this->totalIn - $this->totalOut;

        $this->cardex = CardexModel::where('item_id', $item->id)
            ->where('status', 'final')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->get()
            ->map(function ($record) {
                return [
                    'date' => $record->created_at->toDateString(),
                    'in' => $record->qty_in,
                    'out' => $record->qty_out,
                    'balance' => $record->qty_in - $record->qty_out,
                    'transaction' => $record->transaction_type,
                ];
            });


    }


    public function getData(){
        $this->validate();
        $this->getCardexData($this->itemCode);
    }
    public function updatedItemId($propertyName)
    {
        $this->getCardexData($value);
    }

    public function render()
    {
        return view('livewire.inventory.cardex');
    }
}
