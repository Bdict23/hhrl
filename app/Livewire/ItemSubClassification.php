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

    protected $rules = [
        'classification_name' => 'required|string|max:255',
        'classification_description' => 'nullable|string|max:255',
        'classification_id' => 'required|exists:classifications,id',
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function showAddSubClassification()
    {
        $this->fetchData();
    }


    public function store()
    {

        $this->validate();
        $classification = new Classification();
        $classification->classification_name = $this->classification_name;
        $classification->classification_description = $this->classification_description;
        $classification->class_parent = $this->classification_id;
        $classification->company_id = auth()->user()->branch->company_id;
        $classification->created_by = auth()->user()->emp_id;
        $classification->save();

        $this->reset();
        $this->fetchData();
        session()->flash('success', 'Sub Classification successfully added');
        $this->dispatch('clearSubclassForm');

    }

    public function editSubClassification($id)
    {

        $this->sub_classification = Classification::find($id);
        $this->classification_name = $this->sub_classification->classification_name;
        $this->classification_description = $this->sub_classification->classification_description;
        $this->classification_id = $this->sub_classification->class_parent;
        $this->company_id = $this->sub_classification->company_id;
        $this->classification_id = $this->sub_classification->id;

    }

    public function updateSubClassification(){

        $this->validate();
        $classification = Classification::find($this->classification_id);
        $classification->classification_name = $this->classification_name;
        $classification->classification_description = $this->classification_description;
        $classification->class_parent = $this->classification_id;
        $classification->save();
        $this->reset();
        $this->fetchData();
        session()->flash('success', 'Sub Classification successfully updated');
        $this->dispatch('clearUpdateForm');

    }

    public function fetchData()
    {
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $this->companies = Company::where('company_status', 'ACTIVE')->whereIn('id', $companyIds)->get();
        $this->classifications = Classification::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE']])->wherenull('class_parent')->get();
        $this->sub_classifications = Classification::where([['company_id', auth()->user()->branch->company_id],['status', 'ACTIVE']])->wherenotnull('class_parent')->get();
    }
    public function render()
    {
        return view('livewire.item-sub-classification', [
            'sub_classifications' => $this->sub_classifications,
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
