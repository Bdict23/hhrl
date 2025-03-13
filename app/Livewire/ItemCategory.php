<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Company;
use App\Models\Audit;

class ItemCategory extends Component
{public $categories;
    public $category;
    public $category_description;
    public $category_name;
    public $ItemCategories;

    public $company_id;


    protected $rules = [
        'category_name' => 'required|string|max:255',
        'category_description' => 'required|string|max:255',
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
        $this->ItemCategories = Category::where('company_id', auth()->user()->branch->company_id)->get();
    }

    public function storeCategory()
    {
        try {
            $this->validate();
            $category = new Category();
            $category->category_name = $this->category_name;
            $category->category_type = 'ITEM';
            $category->category_description = $this->category_description;
            $category->company_id = $this->company_id;
            $category->save();
            $this->fetchData();
            $this->category_name = '';
            $this->category_type = '';
            return session()->flash('success', 'Category successfully added');
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }
    public function render()
    {
        return view('livewire.item-category', [
            'categories' => $this->ItemCategories,
            'companies' => $this->companies,
        ]);
    }
}
