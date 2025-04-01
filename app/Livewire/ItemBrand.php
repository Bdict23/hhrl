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

    public $AddBrandTab = 0;
    public $EditBrandTab = 0;
    public $BrandListTab = 0;

    protected $rules = [
        'brand_name' => 'required|string|max:255',
        'brand_description' => 'nullable|string|max:255',
        'company_id' => 'required|integer',
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
        $this->AddBrandTab = 1;
        $this->BrandListTab = 0;
        $this->fetchData();
    }

    public function store()
    {
        $this->AddBrandTab = 1;
        $this->BrandListTab = 0;
        $this->validate();
        $brand = new Brand();
        $brand->brand_name = $this->brand_name;
        $brand->brand_description = $this->brand_description;
        $brand->status = 'ACTIVE';
        $brand->company_id = $this->company_id;
        $brand->created_by = auth()->user()->emp_id;
        $brand->save();
        $this->fetchData();

        $this->brand_name = '';
        $this->brand_description = '';
        $this->company_id = '';

        session()->flash('message', 'Brand Created Successfully.');

        $this->AddBrandTab = 0;
        $this->BrandListTab = 1;
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
