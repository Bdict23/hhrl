<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PriceLevel;
use App\Models\Item;
use App\Models\Branch;

class PriceOperation extends Component
{
    public $priceList = []; // Initialize as an array
    public $itemList = []; // Initialize as an array
    public $branches;
    public $branchCount = 0;
    public $branchIds = [];

    public $itemView = '';
    public $itemBatches = []; // List sa prices sa usa ka item
    public $majorPrice;

    // Declare for active page
    public $AddBatchPricing = 0;
    public $pricingSummary = 0;

    // Declare variables for price calculation
    public $costPrice = 0.00;
    public $retailPrice ;
    public $markupPercentage ;
    public $grossProfitMargin ;
    public $markupAmount ;
    public $taxPercentage = 0.00;
    public $taxAmount = 0.00;
    public $newRetailPrice ;

    public $priceObject;

  


    protected $rules = [
        'retailPrice' => 'required',
        'markupPercentage' => 'required',
        'markupAmount' => 'required',
        'grossProfitMargin' => 'required',
        'taxPercentage' => 'required',
        'taxAmount' => 'required',
        'newRetailPrice' => 'required',
    ]; // Validation rules


    public function mount()
    {
        $this->fetchData();
        $this->newBatch();
    }

    public function fetchData(){
       $this->itemList = Item::with('costPrice')->where('company_id', auth()->user()->branch->company_id)->get();
       $this->priceList = PriceLevel::where('company_id', auth()->user()->branch->company_id)->get();
    }

    public function savePricing()
    {

            $this->AddBatchPricing = 1;
            $this->validate();

            if ($this->branchCount == count($this->branchIds)) {
                $this->priceObject = new PriceLevel();
                $this->priceObject->amount = $this->newRetailPrice;
                $this->priceObject->company_id = auth()->user()->branch->company_id;
                $this->priceObject->price_type = 'SRP';
                $this->priceObject->markup = $this->markupPercentage;
                $this->priceObject->item_id = $this->itemView->id;
                $this->priceObject->save();
            } else {
                foreach ($this->branchIds as $branchId) {
                    $this->priceObject = new PriceLevel();
                    $this->priceObject->amount = $this->newRetailPrice;
                    $this->priceObject->company_id = auth()->user()->branch->company_id;
                    $this->priceObject->price_type = 'SRP';
                    $this->priceObject->markup = $this->markupPercentage;
                    $this->priceObject->item_id = $this->itemView->id;
                    $this->priceObject->branch_id = $branchId;
                    $this->priceObject->save();
                }
            }
            $this->selectItem($this->itemView->id);
            $this->fetchData();

    }

    public function render()
    {
        return view('livewire.price-operation');
    }

    public function selectItem($id)
    {
        $this->AddBatchPricing = 1;
        $this->itemView = Item::where('id', $id)->first(); // Fetch the selected item
        $this->costPrice = $this->itemView->costPrice->amount; // Fetch the cost price of the item
        $this->itemBatches = PriceLevel::with('item', 'branch')
            ->where([
                ['company_id', auth()->user()->branch->company_id],
                ['item_id', $id],
                ['price_type', 'SRP']
            ])
            ->whereNotNull('branch_id')
            ->orderBy('branch_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('branch_id'); // Fetch the latest price for each branch
        $this->majorPrice = PriceLevel::with('item', 'branch')->where('item_id', $id)->where([['price_type', 'SRP'],['branch_id', null]])->orderBy('created_at', 'desc')->first();

        // Check if ang Base price mas updated kaysa sa mga branch price else add e-remove ang branch from the list
        if($this->itemBatches != null && $this->majorPrice != null)
        {
            foreach ($this->itemBatches  as $index => $itemBatch) {
                if($itemBatch->created_at < $this->majorPrice->created_at){
                    $this->itemBatches->forget($index);
                }
            }
            if($this->branchCount != $this->itemBatches->count()){
                $this->itemBatches->push($this->majorPrice);
        }
    }
        $this->addBatchPricing = true;

    }

    public function newBatch()
    {
        $this->branches = Branch::where('company_id', auth()->user()->branch->company_id)->get();
        $this->branchCount = count($this->branches);
    }

    public function updateFromRetail($event){
        $this->retailPrice = $event;
        $this->markupPercentage = 0;
        $this->AddBatchPricing = 1;
        if($this->retailPrice > $this->costPrice && $this->costPrice != 0 && $this->retailPrice != 0 && $this->costPrice != null && $this->retailPrice != null){
            $this->markupPercentage = ((($this->retailPrice - $this->costPrice) / $this->costPrice)  * 100); // Mag calculate sa markup percentage
            $this->markupAmount = $this->retailPrice - $this->costPrice; // Mag calculate sa markup amount
            $this->grossProfitMargin = ((($this->markupAmount )/ $this->retailPrice) * 100); // Mag calculate sa gross profit margin
            $this->newRetailPrice = $this->retailPrice + $this->taxAmount; // Mag calculate sa new retail price

        }else{
            $this->markupPercentage = 0;
            $this->markupAmount = 0;
        }
    }



    public function updateFromMarkup($event){

        $this->markupPercentage = $event;
        $this->retailPrice = 0;
        $this->AddBatchPricing = 1;

        if($this->markupPercentage > 0 && $this->markupPercentage != 0 && $this->costPrice != null && $this->markupPercentage != null){

            $this->markupAmount = ($this->markupPercentage * $this->costPrice) / 100; // Mag calculate sa markup amount
            $this->retailPrice = $this->costPrice * (1 + ($this->markupPercentage / 100)); // Mag calculate sa retail price
            $this->grossProfitMargin = ($this->markupPercentage / (100 + $this->markupPercentage)) * 100;  // Mag calculate sa gross profit margin
            $this->newRetailPrice = $this->retailPrice + $this->taxAmount; // Mag calculate sa new retail price

        }else{
            $this->retailPrice = 0;
            $this->markupAmount = 0;
        }


    }


    public function toggleBranchSelection($branchId)
    {
        if (in_array($branchId, $this->branchIds)) {
            $this->branchIds = array_diff($this->branchIds, [$branchId]);
        } else {
            $this->branchIds[] = $branchId;
        }
    }
}
