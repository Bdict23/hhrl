<?php

namespace App\Livewire\Inventory;

use Livewire\Component;

use App\Models\Location;
use App\Models\PriceLevel;
use App\Models\Cardex as CardexModel;
use App\Models\Item;
use App\Models\StockTransferInfo;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Cardex extends Component
{

    public $cardex = [];
    public $itemCode = null;
    public $itemDescription = null;
    public $locations = [];
    public $location = null;
    public $price = null;
    public $totalBalance = 0;
    public $cardexData = [];
    public $itemId = null;


    protected $listeners = [
        'getCardexData' => 'getCardexData',
    ];
    protected $rules = [
        'itemCode' => 'exists:items,item_code',
    ];
    protected $messages = [
        'itemCode.exists' => 'Invalid',
        'itemCode.required' => 'Item code is required',
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
        // dd($itemCode);
        // $this->validate();
        $this->itemId = Item::where('item_code', $itemCode)->value('id');
        $this->locations = Location::where('branch_id', auth()->user()->branch_id)->where('item_id',$this->itemId)->first();
        $this->location = $this->locations ? $this->locations->location_name : null;
        $cardexData = CardexModel::with('item')->where('item_id', $this->itemId)->where('source_branch_id', auth()->user()->branch_id)->first();
        if (!$cardexData) {
            $this->locations = Location::where('branch_id', auth()->user()->branch_id)->where('item_id',$this->itemId)->first();
            $this->location = $this->locations ? $this->locations->location_name : null;
             $itemRetail = PriceLevel::where('item_id', $this->itemId)
            ->where('branch_id', auth()->user()->branch_id)
            ->where('price_type', 'SRP')
            ->first();
            $this->price = $itemRetail ? $itemRetail->amount : 0;
            $this->addError('itemCode', 'Item has no history.');
            return;
        }
        // dd($cardexData);
        $this->itemDescription = $cardexData->item->item_description;
        $this->cardex = CardexModel::with('receiving', 'withdrawal')->where('item_id', $this->itemId)->where('source_branch_id', auth()->user()->branch_id)->orderBy('created_at', 'desc')->get();
        $this->price = $cardexData->item->sellingPrice->amount ?? '0.00';
       
        $this->totalBalance = intval($cardexData->totalBalanceByItem());

        //  running balance !
        $runningBalance = 0;
        $this->cardexData = CardexModel::where('item_id', $this->itemId)
            ->with(['item', 'invoice', 'receiving', 'stockTransfer', 'withdrawal'])
            ->where('source_branch_id', auth()->user()->branch_id)
            ->where('status', 'final')
            ->orderBy('created_at', 'asc') // sort order para mag effect ang running balance
            ->get()
            ->map(function ($item) use (&$runningBalance) {
                $runningBalance += $item->qty_in - $item->qty_out;
                return [
                    'transaction_type' => $item->transaction_type,
                    'in' => $item->qty_in,
                    'out' => $item->qty_out,
                    'balance' => $runningBalance, // Running balance
                    'created_at' => $item->created_at,
                    'reference' => $item->invoice->invoice_number ?? $item->receiving->receiving_number ?? $item->stockTransfer->stf_number ?? $item->withdrawal->reference_number ?? $item->receiving->RECEIVING_NUMBER ?? 'Beginning Inventory',
                ];
            });

        $this->cardexData = $this->cardexData->toArray();
    }


    public function getData(){
        $this->validate();
        $this->getCardexData($this->itemCode);
    }

    public function render()
    {
        return view('livewire.inventory.cardex');
    }
}
