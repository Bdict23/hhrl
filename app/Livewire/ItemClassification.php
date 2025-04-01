<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Classification;
use App\Models\Company;
use App\Models\Audit;

class ItemClassification extends Component
{

    public $classification;
    public $classifications;
    public $classification_id;
    public $classification_name;
    public $classification_description;
    public $company_id;
    public $companies;

    public $AddClassificationTab = 0;
    public $ClassificationListTab = 0;

    protected $rules = [
        'classification_name' => 'required|string|max:255',
        'classification_description' => 'required|string|max:255',
        'company_id' => 'required|exists:companies,id',

    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function store()
    {

        $this->AddClassificationTab = 1;
        $this->validate();
        $classification = new Classification();
        $classification->classification_name = $this->classification_name;
        $classification->classification_description = $this->classification_description;
        $classification->company_id = $this->company_id;
        $classification->created_by = auth()->user()->emp_id;
        $classification->save();
        $this->fetchData();
        $this->classification_name = '';
        $this->classification_description = '';
        $this->company_id = '';
        $this->AddClassificationTab = 0;
        $this->ClassificationListTab = 1;
        session()->flash('success', 'Classification successfully added');

    }
    public function fetchData()
    {
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $this->companies = Company::where('company_status', 'ACTIVE')->whereIn('id', $companyIds)->get();
        $this->classifications = Classification::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE']])->wherenull('class_parent')->get();
    }

    public function render()
    {
        return view('livewire.item-classification', [
            'classifications' => $this->classifications,
            'companies' => $this->companies,
        ]);
    }


    public function deactivate($id)
    {
        $classification = Classification::find($id);
        $classification->status = 'INACTIVE';
        $classification->save();
        $this->fetchData();
    }
}
