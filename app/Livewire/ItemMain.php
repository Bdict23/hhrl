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
    public $orderPoint;

    public $item_code;
    public $item_description;
    public $uom_id;
    public $category_id;
    public $brand_id;
    public $classification_id;
    public $sub_classification_id; // Corrected property name
    public $cost;
    public $item_barcode;
    public $parent_classification_id; // New property for sub-classification modal


    // MODAL VARIABLES

    // UOM variables
    public $unit_symbol;
    public $unit_description;
    public $unit_name;

    // Category variables
    public $category_name;
    public $category_description;

    // Brand variables
    public $brand_name;
    public $brand_description;

    // Classification variables
    public $classification_name;
    public $classification_description;

    // Sub Classification variables
    public $sub_classification_name;
    public $sub_classification_description;


    protected $rules = [
        'item_code' => 'required|string|max:80|unique:items,item_code',
        'item_description' => 'required|string|max:255',
        'item_barcode' => 'nullable|string|max:100',
        'uom_id' => 'required|exists:unit_of_measures,id',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'classification_id' => 'required|exists:classifications,id',
        'sub_classification_id' => 'nullable|exists:classifications,id', // Corrected validation rule
        'cost' => 'nullable|numeric',
        'orderPoint' => 'required|numeric',
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
            $item->item_code = strtoupper($this->item_code);
            $item->item_description = $this->item_description;
            $item->item_barcode = $this->item_barcode;
            $item->uom_id = $this->uom_id;
            $item->orderpoint = $this->orderPoint;
            $item->category_id = $this->category_id;
            $item->brand_id = $this->brand_id ? $this->brand_id : null;
            $item->classification_id = $this->classification_id;
            $item->sub_class_id = $this->sub_classification_id ?  $this->sub_classification_id : null ; // Corrected property name
            $item->company_id = auth()->user()->branch->company_id;
            $item->created_by = auth()->user()->emp_id;
            $item->save();

            if ($this->cost != null && $this->cost != 0 && $this->cost != '') {
                $priceLevel = new PriceLevel();
                $priceLevel->item_id = $item->id;
                $priceLevel->price_type = 'COST';
                $priceLevel->amount = $this->cost;
                $priceLevel->company_id = auth()->user()->branch->company_id;
                $priceLevel->branch_id = auth()->user()->branch_id;
                $priceLevel->save();
            }

            $this->reset();
            $this->fetchData();
            session()->flash('item-main-success', 'Item successfully added.');
            $this->dispatch('saved');

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
        $this->orderPoint = $this->item->orderpoint;
        $this->classification_id = $this->item->classification_id;
        $this->sub_classification_id = $this->item->sub_class_id;
    }

    public function update(){

        $this->validate(
            [
                'item_code' => 'required|string|max:80|unique:items,item_code,'.$this->item->id,
                'item_description' => 'required|string|max:255',
                'item_barcode' => 'nullable|string|max:100',
                'uom_id' => 'required|exists:unit_of_measures,id',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'classification_id' => 'required|exists:classifications,id',
                'sub_classification_id' => 'nullable|exists:classifications,id', // Corrected validation rule
                'orderPoint' => 'required|numeric',
            ]
        );
        $this->item->item_code = $this->item_code;
        $this->item->item_description = $this->item_description;
        $this->item->item_barcode = $this->item_barcode;
        $this->item->uom_id = $this->uom_id;
        $this->item->category_id = $this->category_id;
        $this->item->brand_id = $this->brand_id ? $this->brand_id : null;
        $this->item->classification_id = $this->classification_id;
        $this->item->orderpoint = $this->orderPoint;
        $this->item->sub_class_id = $this->sub_classification_id ?  $this->sub_classification_id : null ;
        $this->item->company_id = auth()->user()->branch->company_id;
        $this->item->updated_by = auth()->user()->emp_id;
        $this->item->save();

        $this->dispatch('updated');
        $this->reset();
        $this->fetchData();
        return redirect('/settings')->with('item-main-success', 'Item Updated!');
    }


    public function deactivate($id){
        $item = Item::findOrFail($id);
        $item->item_status = 'INACTIVE';
        $item->save();

        $this->dispatch('deleted');
        $this->reset();
        $this->fetchData();
        session()->flash('item-main-success', 'Item successfully deactivated.');
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


    // MODALS

    public function addUom()
    {
        $this->validate([
            'unit_symbol' => 'required|string|max:10',
            'unit_description' => 'required|string|max:255',
            'unit_name' => 'required|string|max:80',
        ]);
        $uom = new UOM();
        $uom->unit_symbol = $this->unit_symbol;
        $uom->unit_description = $this->unit_description;
        $uom->unit_name = $this->unit_name;
        $uom->company_id = auth()->user()->branch->company_id;
        $uom->created_by = auth()->user()->emp_id;
        $uom->save();

        $this->dispatch('uomAdded');
        $this->reset('unit_symbol', 'unit_description', 'unit_name', 'uoms','uom_id');
        session()->flash('item-main-success', 'UOM successfully added.');
        $this->dispatch('propertyAdded');
        $this->fetchData();

    }

    public function addCategory()
    {
       $this->validate([
           'category_name' => 'required|string|max:50',
            'category_description' => 'required|string|max:155',
       ]);
         $category = new Category();
         $category->category_name = $this->category_name;
         $category->category_type = 'ITEM';
         $category->category_description = $this->category_description;
         $category->company_id = auth()->user()->branch->company_id;
         $category->created_by = auth()->user()->emp_id;
         $category->save();
         $this->category_id = $category->id;
         $this->reset('category_name', 'category_description', 'categories');
         session()->flash('item-main-success', 'Category successfully added.');
         $this->dispatch('propertyAdded');
         $this->dispatch('categoryAdded');
         $this->fetchData();
    }

    public function addBrand()
    {
        $this->validate([
            'brand_name' => 'required|string|max:50',
        ]);
        $brand = new Brand();
        $brand->brand_name = $this->brand_name;
        $brand->company_id = auth()->user()->branch->company_id;
        $brand->created_by = auth()->user()->emp_id;
        $brand->save();
        $this->brand_id = $brand->id;
        $this->dispatch('brandAdded');
        $this->reset('brand_name', 'brands');
        session()->flash('item-main-success', 'Brand successfully added.');
        $this->dispatch('propertyAdded');
        $this->fetchData();
    }


    public function addClassification()
    {
        $this->validate([
            'classification_name' => 'required|string|max:50',
        ]);
        $classification = new Classification();
        $classification->classification_name = $this->classification_name;
        $classification->company_id = auth()->user()->branch->company_id;
        $classification->created_by = auth()->user()->emp_id;
        $classification->save();
        $this->classification_id = $classification->id;
        $this->dispatch('classificationAdded');
        $this->reset('classification_name', 'classifications');
        session()->flash('item-main-success', 'Classification successfully added.');
        $this->dispatch('propertyAdded');
        $this->fetchData();
    }

    public function addSubClassification(){
        $this->validate([
            'sub_classification_name' => 'required|string|max:50',
            'parent_classification_id' => 'required|exists:classifications,id', // Updated to use new property
            'sub_classification_description' => 'nullable|string|max:155',
        ]);
    //  dd("parent: {$this->parent_classification_id}, sub name: {$this->sub_classification_name}, test: {$this->sub_classification_description}");
        $classification = new Classification();
        $classification->classification_name = $this->sub_classification_name;
        $classification->classification_description = $this->sub_classification_description;
        $classification->class_parent = $this->parent_classification_id; // Updated to use new property
        $classification->company_id = auth()->user()->branch->company_id;
        $classification->created_by = auth()->user()->emp_id;
        $classification->save();
        // dd('classification saved');
        $this->sub_classification_id = $classification->id;
        $this->dispatch('subClassificationAdded');
        $this->reset('sub_classification_name', 'sub_classification_description', 'parent_classification_id'); // Reset new property
        session()->flash('item-main-success', 'Sub Classification successfully added.');
        $this->dispatch('propertyAdded');
        $this->fetchData();
    }

}
