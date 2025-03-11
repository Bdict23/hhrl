<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Classification;
use App\Models\Company;
use App\Models\Audit;

class ItemClassification extends Component
{

    public $classification;
    public $classification_id;
    public $classification_name;
    public $classification_status;
    public $classifications;

    protected $rules = [
        'classification_name' => 'required|string|max:255',
        'classification_status' => 'required|string|max:255',
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
        $this->classifications = Classification::where('company_id', auth()->user()->branch->company_id)->wherenull('class_parent')->get();
    }

    public function render()
    {
        return view('livewire.item-classification', [
            'classifications' => $this->classifications,
            'companies' => $this->companies,
        ]);
    }
}
