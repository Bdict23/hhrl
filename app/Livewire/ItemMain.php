<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item;
use App\Models\Company;
use App\Models\UOM;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Brand;
use App\Models\Audit;
use App\Models\PriceLevel;


class ItemMain extends Component
{

    public $items;
    public $item;
    public $uoms;
    public $categories;
    public $classifications;
    public $brands;
    public $sub_classifications;

    public $item_code;
    public $item_description;
    public $uom_id;
    public $category_id;
    public $brand_id;
    public $classification_id;
    public $sub_classification_id; // Corrected property name
    public $cost;
    public $item_barcode;


    protected $rules = [
        'item_code' => 'required|string|max:80',
        'item_description' => 'required|string|max:255',
        'item_barcode' => 'nullable|string|max:100',
        'uom_id' => 'required|exists:unit_of_measures,id',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'classification_id' => 'required|exists:classifications,id',
        'sub_classification_id' => 'nullable|exists:classifications,id', // Corrected validation rule
        'cost' => 'nullable|numeric'
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->items = Item::where([['company_id', auth()->user()->branch->company_id],['item_status', 'ACTIVE']])->get();
        $this->uoms = UOM::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE']])->get();
        $this->categories = Category::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE'],['category_type', 'ITEM']])->get();
        $this->classifications = Classification::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE'],['class_parent', null]])->get();
        $this->brands = Brand::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE']])->get();
        $this->sub_classifications = Classification::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE'],['class_parent', '!=', null]])->get();
    }

    public function store()
    {

            $this->validate();
            $item = new Item();
            $item->item_code = $this->item_code;
            $item->item_description = $this->item_description;
            $item->item_barcode = $this->item_barcode;
            $item->uom_id = $this->uom_id;
            $item->category_id = $this->category_id;
            $item->brand_id = $this->brand_id;
            $item->classification_id = $this->classification_id;
            $item->sub_class_id = $this->sub_classification_id; // Corrected property name
            $item->company_id = auth()->user()->branch->company_id;
            $item->created_by = auth()->user()->emp_id;
            $item->save();

            if ($this->cost != null && $this->cost != 0 && $this->cost != '') {
                $priceLevel = new PriceLevel();
                $priceLevel->item_id = $item->id;
                $priceLevel->price_type = 'COST';
                $priceLevel->amount = $this->cost;
                $priceLevel->company_id = auth()->user()->branch->company_id;
                $priceLevel->save();
            }

            $this->dispatch('saved');
            $this->reset();
            $this->fetchData();
            session()->flash('message', 'Item successfully added.');

    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function edit($id)
    {
        $this->item = Item::with(['uom', 'category', 'brand', 'classification', 'sub_classification', 'costPrice'])
            ->findOrFail($id);
        $this->item_code = $this->item->item_code;
        $this->item_description = $this->item->item_description;
        $this->uom_id = $this->item->uom_id;
        $this->category_id = $this->item->category_id;
        $this->brand_id = $this->item->brand_id;
        $this->classification_id = $this->item->classification_id;
        $this->sub_classification_id = $this->item->sub_class_id;
    }

    public function update(){

        $this->validate();
        $this->item->item_code = $this->item_code;
        $this->item->item_description = $this->item_description;
        $this->item->uom_id = $this->uom_id;
        $this->item->category_id = $this->category_id;
        $this->item->brand_id = $this->brand_id;
        $this->item->classification_id = $this->classification_id;
        $this->item->sub_class_id = $this->sub_classification_id;
        $this->item->company_id = auth()->user()->branch->company_id;
        $this->item->updated_by = auth()->user()->emp_id;
        $this->item->save();

        $this->dispatch('updated');
        $this->reset();
        $this->fetchData();
        session()->flash('message', 'Item successfully updated.');
    }


    public function deactivate($id){
        $item = Item::findOrFail($id);
        $item->item_status = 'INACTIVE';
        $item->save();

        $this->dispatch('deleted');
        $this->reset();
        $this->fetchData();
        session()->flash('message', 'Item successfully deactivated.');
    }

    public function render()
    {
        return view('livewire.item-main', [
            'items' => $this->items,
            'uoms' => $this->uoms,
            'categories' => $this->categories,
            'classifications' => $this->classifications,
            'brands' => $this->brands,
            'sub_classifications' => $this->sub_classifications
        ]);
    }


}
