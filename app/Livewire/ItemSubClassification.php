<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Classification;
use App\Models\Company;
use App\Models\Audit;


class ItemSubClassification extends Component
{
    public $sub_classifications;
    public $sub_classification;
    public $classification_id;
    public $classifications;
    public $companies;


    protected $rules = [
        'classification_name' => 'required|string|max:255',
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
        $this->classifications = Classification::where('company_id', auth()->user()->branch->company_id)->get();
        $this->sub_classifications = Classification::where('company_id', auth()->user()->branch->company_id)->wherenotnull('class_parent')->get();

    }
    public function render()
    {
        return view('livewire.item-sub-classification', [
            'sub_classifications' => $this->sub_classifications,
            'classifications' => $this->classifications,
            'companies' => $this->companies,
        ]);
    }
}
