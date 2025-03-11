<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item;
use App\Models\Company;
use App\Models\UOM;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Brand;

class ItemLists extends Component
{
    public $items;
    public $companies;
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
    public $sub_class_id;
    public $company_id;

    protected $rules = [
        'item_code' => 'required|string|max:255',
        'item_description' => 'required|string|max:255',
        'uom_id' => 'required|exists:unit_of_measures,id',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'classification_id' => 'required|exists:classifications,id',
        'sub_class_id' => 'required|exists:classifications,id',
        'company_id' => 'required|exists:companies,id',
    ];
    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->items = Item::where('company_id', auth()->user()->branch->company_id)->get();
        $this->companies = Company::where([['company_status', 'ACTIVE'], ['created_by', auth()->user()->emp_id]])->get();
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
        $item->uom_id = $this->uom_id;
        $item->category_id = $this->category_id;
        $item->brand_id = $this->brand_id;
        $item->classification_id = $this->classification_id;
        $item->sub_class_id = $this->sub_class_id;
        $item->company_id = $this->company_id;
        $item->created_by = auth()->user()->emp_id;
        $item->save();

        dd('Item successfully added.');
        // session()->flash('message', 'Item successfully added.');

    }



    public function render()
    {
        return view('livewire.item-lists', [
            'items' => $this->items,
            'companies' => $this->companies,
            'uoms' => $this->uoms,
            'categories' => $this->categories,
            'classifications' => $this->classifications,
            'brands' => $this->brands,
            'sub_classifications' => $this->sub_classifications,
        ]);
    }
}
