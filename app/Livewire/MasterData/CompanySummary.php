<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Company;
use App\Models\Audit;
use App\Models\Branch;  

class CompanySummary extends Component
{
    public $companies = [];
    public $auditCompanies = [];
    public $companyIds = [];

    // create fields
    public $comp_name = '';
    public $comp_code = '';
    public $comp_tin = '';
    public $comp_type = '';
    public $comp_desc = '';

    protected $rules = [
        'comp_name' => 'required|string|max:55|unique:companies,company_name',
        'comp_code' => 'required|string|max:25|unique:companies,company_code',
        'comp_tin' => 'required|numeric|unique:companies,company_tin',
        'comp_type' => 'required|string|max:155',
        'comp_desc' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->fetchCompanySummary();
    }


    public function fetchCompanySummary()
    {
        $this->auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $this->companyIds = $this->auditCompanies->pluck('company.id')->toArray();
        $this->companies = Company::where('company_status', 'active')->whereIn('id', $this->companyIds)->get();
    }

    public function render()
    {
        return view('livewire.master-data.company-summary');
    }

// Create a new company
    public function createCompany()
    {
        $this->validate();

        $company = new Company();
        $company->company_name = $this->comp_name;
        $company->company_code = $this->comp_code;
        $company->company_tin = $this->comp_tin;
        $company->company_type = $this->comp_type;
        $company->company_description = $this->comp_desc;
        $company->save();
        $audit = new Audit();
        $audit->company_id = $company->id;
        $audit->created_by = auth()->user()->emp_id;
        $audit->save();

        $this->reset(['comp_name', 'comp_code', 'comp_tin', 'comp_type', 'comp_desc']);

        $this->fetchCompanySummary();
        session()->flash('success', 'Company created successfully!');
        $this->dispatch('dispatch-success');
    }
}
