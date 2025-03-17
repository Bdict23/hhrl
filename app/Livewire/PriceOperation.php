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
    public $selectAllBrach = 0;

    public $itemView = '';
    public $itemBatches = []; // List sa prices sa usa ka item
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
        try {
            $this->AddBatchPricing = 1;
            $this->validate();
            if ($this->selectAllBrach == 1) {
                $this->priceObject = new PriceLevel();
                $this->priceObject->amount = $this->newRetailPrice;
                $this->priceObject->company_id = auth()->user()->branch->company_id;
                $this->priceObject->price_type = 'SRP';
                $this->priceObject->markup = $this->markupPercentage;
                $this->priceObject->item_id = $this->itemView->id;
                $this->priceObject->save();
            } else {
                dd($this->branchIds[0]);
                foreach ($this->branchIds as $index => $branchId) {
                    dd($this->branchIds[$index]);
                    $this->priceObject = new PriceLevel();
                    $this->priceObject->amount = $this->newRetailPrice;
                    $this->priceObject->company_id = auth()->user()->branch->company_id;
                    $this->priceObject->price_type = 'SRP';
                    $this->priceObject->markup = $this->markupPercentage;
                    $this->priceObject->item_id = $this->itemView->id;
                    $this->priceObject->branch_id = $branchId[$index];
                    $this->priceObject->save();
                }
            }
            $this->fetchData();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.price-operation');
    }

    public function selectItem($id){

        $this->AddBatchPricing = 1;
        $this->itemView = Item::where('id', $id)->first(); // Mag fetch sa item nga gi select sa modal
        $this->costPrice = $this->itemView->costPrice->amount; // Mag fetch sa cost price sa item
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

    public function toggleSelectAllBranches(){
        if($this->selectAllBrach == 1){
            $this->selectAllBrach = 0;
        }else{
            $this->selectAllBrach = 1;

        }
        $this->AddBatchPricing = 1;


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
