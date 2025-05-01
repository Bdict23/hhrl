<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Branch;
use App\Models\ProgramSetting as ProgramSettingsModel;

class ProgramSettings extends Component
{
    public $branches = [];
    public $programSettings = [];

    public function mount()
    {
        // Initialization code can go here if needed
        $this->refresh();
    }

    public function refresh()
    {
        $this->branches = Branch::all();
        $this->programSettings = ProgramSettingsModel::all();
    }
    public function render()
    {
        return view('livewire.settings.program-settings');
    }
}
