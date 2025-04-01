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


    protected $rules = [
        'classification_name' => 'required|string|max:55',
        'classification_description' => 'required|string|max:100',

    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function store()
    {

        $this->validate();
        $classification = new Classification();
        $classification->classification_name = $this->classification_name;
        $classification->classification_description = $this->classification_description;
        $classification->company_id = auth()->user()->branch->company_id;
        $classification->created_by = auth()->user()->emp_id;
        $classification->save();
        $this->fetchData();
        $this->classification_name = '';
        $this->classification_description = '';
        $this->company_id = '';
        session()->flash('success', 'Classification successfully added');
        $this->dispatch('clearForm');

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

    public function editClassification($id)
    {
        $this->classification = Classification::find($id);
        $this->classification_id = $this->classification->id;
        $this->classification_name = $this->classification->classification_name;
        $this->classification_description = $this->classification->classification_description;
    }

    public  function updateClassification()
    {
        $this->validate();
        $classification = Classification::find($this->classification_id);
        $classification->classification_name = $this->classification_name;
        $classification->classification_description = $this->classification_description;
        $classification->updated_by = auth()->user()->emp_id;
        $classification->save();
        $this->reset('classification_name', 'classification_description');
        $this->fetchData();
        session()->flash('success', 'Classification successfully updated');
        $this->dispatch('clearClassificationModalUpdateForm');
    }


    public function deactivate($id)
    {
        $classification = Classification::find($id);
        $classification->status = 'INACTIVE';
        $classification->save();
        $this->fetchData();
    }
}
