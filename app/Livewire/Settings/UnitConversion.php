<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\UnitConversion as UnitConversionModel;
use App\Models\UOM; // Assuming UOM model exists in App\Models namespace

class UnitConversion extends Component
{
    public $unitOfMeasures = [];
    public $UOM;
    public $fromUOM;
    public $toUOM;
    public $conversionFactor;
    protected $rules = [
        'fromUOM' => 'required|exists:unit_of_measures,id',
        'toUOM' => 'required|exists:unit_of_measures,id',
        'conversionFactor' => 'required|numeric|min:0.001',
    ];
    public function render()
    {
        return view('livewire.settings.unit-conversion');
    }

    public function mount()
    {
        // Initialization logic can go here if needed
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->UOM = UOM::where('status', 'ACTIVE')->where('company_id', auth()->user()->branch->company_id)->get();
        $this->unitOfMeasures = UnitConversionModel::with(['From', 'To'])->get();
    }

    public function storeUnitConversion()
    {
        $this->validate();

        // Check if the conversion already exists
        $existingConversion = UnitConversionModel::where('from_uom_id', $this->fromUOM)
            ->where('to_uom_id', $this->toUOM)
            ->first();

        if ($existingConversion) {
            session()->flash('error', 'Conversion already exists.');
            return;
        }

        // Create new unit conversion
        UnitConversionModel::create([
            'from_uom_id' => $this->fromUOM,
            'to_uom_id' => $this->toUOM,
            'conversion_factor' => $this->conversionFactor,
        ]);

        session()->flash('success', 'Unit conversion created successfully.');
        $this->reset();
        $this->fetchData();
        $this->dispatch('unitConversionAdded'); // Dispatch an event to notify other components
    }
}
