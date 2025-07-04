<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Service as ServiceModel; // Assuming Service model exists in App\Models namespace
use App\Models\PriceLevel; // Assuming PriceLevel model exists in App\Models namespace
use App\Models\Category; // Assuming Category model exists in App\Models namespace

class Service extends Component
{
    public $services = [];
    public $categories = [];
    public $service_type_input; // New property for service type
    public $service_id;
    public $service_name_input;
    public $service_code_input;
    public $service_description_input;
    public $service_rate_input;
    public $service_multiplier_input;
    public $service_category_add_input;
    public $service_category_description_input;
    public $oldPrice;
    public $oldCost;
    public $selectedCategoryId;
    public $service_cost_input = 0;

    protected $rules = [
        'service_name_input' => 'required|string|max:255',
        'service_code_input' => 'required|string|max:255|unique:services,service_code',
        'service_description_input' => 'required|string|max:1000',
        'service_rate_input' => 'required|numeric|min:0',
        'service_multiplier_input' => 'nullable|boolean',
        'selectedCategoryId' => 'required|exists:categories,id',
        'service_type_input' => 'required|in:INTERNAL,EXTERNAL', // Validation for service type
        'service_cost_input' => 'nullable|numeric|min:0',
        'service_cost_input' => 'lt:service_rate_input',
    ];

    protected $messages = [
        'service_name_input.required' => 'Service name is required.',
        'service_code_input.required' => 'Service code is required.',
        'service_code_input.unique' => 'Service code must be unique.',
        'service_rate_input.required' => 'Service rate is required.',
        'selectedCategoryId.required' => 'Please select a category for the service.',
        'service_type_input.required' => 'Service type is required.',
        'service_type_input.in' => 'Service type must be either INTERNAL or EXTERNAL.',
    ];

    
    public function render()
    {
        return view('livewire.settings.service');
    }
    public function mount()
    {
        // Initialization logic can go here
        $this->fetchData();
    }
    public function fetchData()
    {
       $this->services = ServiceModel::with('ratePrice','category','costPrice')
            ->where([['status', 'ACTIVE'], ['branch_id', auth()->user()->branch_id]])
            ->get();
        $this->categories = Category::where('category_type', 'SERVICE')
            ->where('company_id', auth()->user()->branch->company_id)->get();
            // dd($this->categories);
    }
    public function storeService()
    {
        $this->validate();
        $service = ServiceModel::create([
            'service_name' => $this->service_name_input,
            'service_code' => $this->service_code_input,
            'service_type' => $this->service_type_input, // Store service type
            'service_description' => $this->service_description_input,
            'category_id' => $this->selectedCategoryId,
            'branch_id' => auth()->user()->branch_id,
            'has_multiplier' => $this->service_multiplier_input,
            'status' => 'ACTIVE',
            'created_by' => auth()->user()->id,
        ]);

        PriceLevel::create([
            'price_type' => 'RATE',
            'amount' => $this->service_rate_input,
            'created_by' => auth()->user()->id,
            'branch_id' => auth()->user()->branch_id,
            'company_id' => auth()->user()->branch->company_id,
            'service_id' => $service->id,
        ]);

        if ($this->service_type_input === 'EXTERNAL') {
            PriceLevel::create([
            'price_type' => 'COST',
            'amount' => $this->service_cost_input,
            'created_by' => auth()->user()->id,
            'branch_id' => auth()->user()->branch_id,
            'company_id' => auth()->user()->branch->company_id,
            'service_id' => $service->id,
        ]);
        }

        session()->flash('success', 'Service created successfully.');
        $this->dispatch('clearForm');
        $this->reset();

        $this->fetchData();
    }

    public function editService($serviceId)
    {
        $this->service_id = $serviceId;
        $service = ServiceModel::findOrFail($serviceId);
        if (!$service) {
            session()->flash('error', 'Service not found.');
            return;
        }
        $this->service_name_input = $service->service_name;
        $this->service_type_input = $service->service_type; // Set service type for editing
        $this->service_code_input = $service->service_code;
        $this->service_description_input = $service->service_description;
        $this->selectedCategoryId = $service->category_id;
        $this->service_rate_input = $service->ratePrice ? $service->ratePrice->amount : null;
        $this->oldPrice = $service->ratePrice ? $service->ratePrice->amount : null;
        $this->service_cost_input = $service->costPrice ? $service->costPrice->amount : 0;
        $this->oldCost = $service->costPrice ? $service->costPrice->amount : 0;
        $this->service_multiplier_input = $service->has_multiplier;

    }

    public function updateService()
    {
        $this->validate(
            [
                'service_name_input' => 'required|string|max:255',
                'service_code_input' => 'required|string|max:255|unique:services,service_code,' . $this->service_id,
                'service_description_input' => 'required|string|max:1000',
                'service_rate_input' => 'nullable|numeric|min:0',
                'selectedCategoryId' => 'required|exists:categories,id',
                'service_multiplier_input' => 'nullable|boolean',
                'service_type_input' => 'required|in:INTERNAL,EXTERNAL',
                'service_cost_input' => 'nullable|numeric|min:0',
                'service_cost_input' => 'lt:service_rate_input',
            ]
        );
        $service = ServiceModel::findOrFail($this->service_id);
        if (!$service) {
            session()->flash('error', 'Service not found.');
            return;
        }

        $service->update([
            'service_name' => $this->service_name_input,
            'service_code' => $this->service_code_input,
            'service_type' => $this->service_type_input, // Update service type
            'service_description' => $this->service_description_input,
            'category_id' => $this->selectedCategoryId,
            'has_multiplier' => $this->service_multiplier_input,
        ]);

        if ($this->oldPrice !== $this->service_rate_input) {
                PriceLevel::create([
                    'price_type' => 'RATE',
                    'amount' => $this->service_rate_input,
                    'created_by' => auth()->user()->id,
                    'branch_id' => auth()->user()->branch_id,
                    'company_id' => auth()->user()->branch->company_id,
                    'service_id' => $service->id,
                    'created_at' => now('Asia/Manila'),
                ]);
            }
        if ($this->service_type_input === 'EXTERNAL' && $this->oldCost !== $this->service_cost_input) {
                PriceLevel::create([
                    'price_type' => 'COST',
                    'amount' => $this->service_cost_input,
                    'created_by' => auth()->user()->id,
                    'branch_id' => auth()->user()->branch_id,
                    'company_id' => auth()->user()->branch->company_id,
                    'service_id' => $service->id,
                    'created_at' => now('Asia/Manila'),
                ]);
            }

        session()->flash('success', 'Service updated successfully.');
        $this->dispatch('hideUpdateServiceModal');
        $this->reset();

        $this->fetchData();
    }
    

    public function deactivateService($serviceId)
    {
        $service = ServiceModel::findOrFail($serviceId);
        if (!$service) {
            session()->flash('error', 'Service not found.');
            return;
        }
        $service->update(['status' => 'INACTIVE']);

        $this->reset();
        session()->flash('success', 'Service deactivated successfully.');
        $this->fetchData();
    }

    public function storeServiceCategory()
    {
        $this->validate([
            'service_category_add_input' => 'required|string|max:255|unique:categories,category_name',
            'service_category_description_input' => 'nullable|string|max:100',
        ]);

        Category::create([
            'category_name' => $this->service_category_add_input,
            'category_description' => $this->service_category_description_input,
            'category_type' => 'SERVICE',
            'company_id' => auth()->user()->branch->company_id,
            'created_by' => auth()->user()->id,
        ]);

        session()->flash('success', 'Service category created successfully.');
        $this->dispatch('clearCategoryForm');
        $this->reset(['service_category_add_input', 'service_category_description_input']);
        $this->fetchData();
    }


}
