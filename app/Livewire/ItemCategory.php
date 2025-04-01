<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Company;
use App\Models\Audit;

class ItemCategory extends Component
{
    public $category_id;
    public $categories;
    public $category;
    public $category_description_input;
    public $category_name_input;
    public $ItemCategories;
    public $companies;
    public $company_id;


    protected $rules = [
        'category_name_input' => 'required|string|max:255',
        'category_description_input' => 'required|string|max:255',
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
        $this->ItemCategories = Category::where([['company_id', auth()->user()->branch->company_id],['category_type', 'ITEM'], ['status', 'ACTIVE']])->get();
    }

    public function storeCategory()
    {
            $this->validate();
            $category = new Category();
            $category->category_name = $this->category_name_input;
            $category->category_type = 'ITEM';
            $category->category_description = $this->category_description_input;
            $category->company_id = auth()->user()->branch->company_id;
            $category->save();
            $this->reset('category_name_input', 'category_description_input');
            $this->fetchData();
            session()->flash('success', 'Category successfully added');
            $this->dispatch('clearForm');
            $this->reset();
            $this->fetchData();

    }
    public function render()
    {
        return view('livewire.item-category', [
            'categories' => $this->ItemCategories,
            'companies' => $this->companies,
        ]);
    }

    public function editCategory($id)
    {
        $this->category = Category::find($id);
        $this->category_name_input = $this->category->category_name;
        $this->category_description_input = $this->category->category_description;
    }
    public function updateCategory()
    {
        $this->validate();
        $category = Category::find($this->category->id);
        $category->category_name = $this->category_name_input;
        $category->category_description = $this->category_description_input;
        $category->save();
        $this->reset('category_name_input', 'category_description_input');
        $this->fetchData();
        session()->flash('success', 'Category successfully updated');
        $this->dispatch('hideUpdateCategoryModal');
        $this->dispatch('clearCategoryModalUpdate');
    }

    public function deactivate( $id)
    {
        $category = Category::find($id);
        $category->status = 'INACTIVE';
        $category->save();
        $this->fetchData();
    }
}
