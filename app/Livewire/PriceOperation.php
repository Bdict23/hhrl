<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PriceLevel;
use App\Models\Item;
use App\Models\Branch;

class PriceOperation extends Component
{
    public $priceList = []; // Initialize as an array
    public $costPrice;
    public $retailPrice;
    public $itemList = []; // Initialize as an array
    public $branches;

    public $itemView = '';
    public $itemBatches = []; // List sa prices sa usa ka item
    public $AddBatchPricing = 0;
    public $pricingSummary = 0;

    protected $rules = ['priceUpdated' => 'updatePrice'];


    public function mount()
    {
        $this->fetchData();
        $this->newBatch();
    }

    public function fetchData(){
       $this->itemList = Item::with('costPrice')->where('company_id', auth()->user()->branch->company_id)->get();
       $this->priceList = PriceLevel::where('company_id', auth()->user()->branch->company_id)->get();
    }

    public function store()
    {
        $this->AddBatchPricing = 1;
        $this->validate(['priceList' => 'required|numeric']);
        PriceLevel::create([
            'price' => $this->priceList,
            'company_id' => auth()->user()->branch->company_id,
        ]);
        $this->fetchData();
    }

    public function render()
    {
        return view('livewire.price-operation');
    }

    public function selectItem($id){

        $this->AddBatchPricing = 1;
        $this->itemView = Item::where('id', $id)->first(); // Mag fetch sa item nga gi select sa modal
        $this->itemBatches = PriceLevel::with('item')
            ->where([
            ['company_id', auth()->user()->branch->company_id],
            ['item_id', $id],
            ['price_type', 'SRP']
            ])
            ->whereNotNull('branch_id')
            ->get(); // Mag fetch sa mga price sa item
        $this->addBatchPricing = true;

    }

    public function newBatch(){
        $this->branches = Branch::where('company_id', auth()->user()->branch->company_id)->get();
    }
}
