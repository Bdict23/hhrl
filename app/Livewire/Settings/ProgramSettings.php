<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Branch;
use App\Models\ProgramSetting as ProgramSettingsModel;
use App\Models\BranchSettingConfig;

class ProgramSettings extends Component
{
    public $branches = [];
    public $programSettings = [];
    public $branchConfiguration = [];

    public function mount()
    {
        // Initialization code can go here if needed
        $this->refresh();
    }

    public function refresh()
    {
        $this->branches = Branch::all();
        $this->programSettings = ProgramSettingsModel::all();

        foreach ($this->branches as $branch) {
            foreach ($this->programSettings as $setting) {
                // Check if the setting is already configured for the branch
                $existingSetting = BranchSettingConfig::where('branch_id', $branch->id)
                    ->where('setting_id', $setting->id)
                    ->first();
                if ($existingSetting) {
                    // If it exists, use the existing value
                    $this->branchConfiguration[$branch->id][$setting->id] = $existingSetting->value;
                } else {
                    // If it doesn't exist, set a default value (e.g., null or empty string)
                    $this->branchConfiguration[$branch->id][$setting->id] = 0;
                }
            }
        }
    }

    public function setBranchConfiguration($branchId, $settingId, $value)
    {
        // Validate the input
        $this->validate([
            "branchConfiguration.$branchId.$settingId" => 'required|boolean',
        ]);

        // Update or create the branch setting configuration
        BranchSettingConfig::updateOrCreate(
            [
                'branch_id' => $branchId,
                'setting_id' => $settingId,
            ],
            [
                'value' => $value,
            ]
        );
    }
    public function render()
    {
        return view('livewire.settings.program-settings');
    }
}
