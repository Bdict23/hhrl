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
    public $classifications;
    public $companies;

    public $classification_id;
    public $classification_name;
    public $classification_description;
    public $company_id;

    public $AddSubClassificationTab = 0;
    public $SubClassificationListTab = 0;


    protected $rules = [
        'classification_name' => 'required|string|max:255',
        'classification_description' => 'required|string|max:255',
        'classification_id' => 'required|exists:classifications,id',
        'company_id' => 'required|exists:companies,id',
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function showAddSubClassification()
    {
        $this->AddSubClassificationTab = 1;
        $this->SubClassificationListTab = 0;
        $this->fetchData();
    }


    public function store()
    {

        $this->AddSubClassificationTab = 1;
        $this->SubClassificationListTab = 0;
        $this->validate();
        $classification = new Classification();
        $classification->classification_name = $this->classification_name;
        $classification->classification_description = $this->classification_description;
        $classification->class_parent = $this->classification_id;
        $classification->company_id = $this->company_id;
        $classification->created_by = auth()->user()->emp_id;
        $classification->save();
        $this->fetchData();
        $this->AddSubClassificationTab = 0;
        $this->SubClassificationListTab = 1;

        $this->classification_name = '';
        $this->classification_description = '';
        $this->classification_id = '';
        $this->company_id = '';
        session()->flash('success', 'Sub Classification successfully added');

    }

    public function fetchData()
    {
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $this->companies = Company::where('company_status', 'ACTIVE')->whereIn('id', $companyIds)->get();
        $this->classifications = Classification::where('company_id', auth()->user()->branch->company_id)->wherenull('class_parent')->get();
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
