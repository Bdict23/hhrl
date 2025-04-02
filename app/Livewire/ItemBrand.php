<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Brand;
use App\Models\Company;
use App\Models\Audit;


class ItemBrand extends Component
{
    public $itemBrand;
    public $itemBrands;

    public $brand_name;
    public $brand_description;

    public $companies;
    public $company_id;


    protected $rules = [
        'brand_name' => 'required|string|max:255',
        'brand_description' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $this->companies = Company::where('company_status', 'ACTIVE')->whereIn('id', $companyIds)->get();
        $this->itemBrands = Brand::where('status', 'ACTIVE')->get();
    }


    public function showAddBrand()
    {
        $this->fetchData();
    }

    public function store()
    {
        $this->validate();
        $brand = new Brand();
        $brand->brand_name = $this->brand_name;
        $brand->brand_description = $this->brand_description;
        $brand->status = 'ACTIVE';
        $brand->company_id = auth()->user()->branch->company_id;
        $brand->created_by = auth()->user()->emp_id;
        $brand->save();

        $this->reset();
        $this->fetchData();

        session()->flash('success', 'Brand Created Successfully.');
        $this->dispatch('clearBrandForm');

    }

    public function editBrand($id)
    {
        $this->itemBrand = Brand::find($id);
        $this->brand_name = $this->itemBrand->brand_name;
        $this->brand_description = $this->itemBrand->brand_description;
    }

    public function updateBrand()
    {
        $this->validate();
        $brand = Brand::find($this->itemBrand->id);
        $brand->brand_name = $this->brand_name;
        $brand->brand_description = $this->brand_description;
        $brand->save();

        $this->reset();
        $this->fetchData();

        session()->flash('success', 'Brand Updated Successfully.');
        $this->dispatch('clearBrandUpdateModal');
    }

    public function render()
    {
        return view('livewire.item-brand', [
            'itemBrands' => $this->itemBrands
        ]);
    }


    public function deactivate($id)
    {
        $brand = Brand::find($id);
        $brand->status = 'INACTIVE';
        $brand->save();
        $this->fetchData();
        session()->flash('message', 'Brand Deactivated Successfully.');
    }

}
