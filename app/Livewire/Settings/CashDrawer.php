<?php

namespace App\Livewire\Settings;
use App\Models\CashDrawer as CashDrawerModel; 
use App\Models\Department;
use Livewire\Component;

class CashDrawer extends Component
{
    public $cashDrawers = [];
    public $departments = [];

    // form fields
    public $drawer_name_input;
    public $drawer_code_input;
    public $selected_department_id;
    public $drawerId;

    public function render()
    {
        return view('livewire.settings.cash-drawer');
    }
    public function mount()
    {
        // Initialization logic can go here
        $this->fetchCashDrawerData();
    }
    public function fetchCashDrawerData()
    {
        $this->cashDrawers = CashDrawerModel::where('branch_id', auth()->user()->branch_id)->where('drawer_status', 'ACTIVE')->get();
        $this->departments = Department::where('branch_id', auth()->user()->branch_id)->get();
    }  
    
    public function storeCashDrawer()
    {
        try{
         
        $validatedData = $this->validate([
            'drawer_name_input' => 'required|string|max:255',
            'drawer_code_input' => 'required|string|max:255|unique:cash_drawers,drawer_code',
            'selected_department_id' => 'required|exists:departments,id',
        ]);

        CashDrawerModel::create([
            'drawer_name' => $this->drawer_name_input,
            'drawer_code' => $this->drawer_code_input,
            'department_id' => $this->selected_department_id,
            'branch_id' => auth()->user()->branch_id,
            'drawer_status' => 'ACTIVE',
            'created_by' => auth()->user()->employee->id,
        ]);

        // Reset form fields
        $this->drawer_name_input = '';
        $this->drawer_code_input = '';
        $this->selected_department_id = null;

        // Refresh the cash drawer list
        $this->fetchCashDrawerData();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Cash Drawer added successfully!']);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function deactivateCashDrawer($id)
    {
        $cashDrawer = CashDrawerModel::find($id);
        if ($cashDrawer) {
            $cashDrawer->drawer_status = 'INACTIVE';
            $cashDrawer->save();
            $this->fetchCashDrawerData();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Cash Drawer deactivated successfully!']);
        } else {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Cash Drawer not found.']);
        }
    }

    public function editCashDrawer($id)
    {
        $cashDrawer = CashDrawerModel::find($id);
        if ($cashDrawer) {
            $this->drawerId = $id;
            $this->drawer_name_input = $cashDrawer->drawer_name;
            $this->drawer_code_input = $cashDrawer->drawer_code;
            $this->selected_department_id = $cashDrawer->department_id;
        } else {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Cash Drawer not found.']);
        }
    }
    public function updateDrawer()
    {
        try {
            $validatedData = $this->validate([
                'drawer_name_input' => 'required|string|max:255',
                'drawer_code_input' => 'required|string|max:255|unique:cash_drawers,drawer_code,' . $this->drawerId,
                'selected_department_id' => 'required|exists:departments,id',
            ]);

            $cashDrawer = CashDrawerModel::where('branch_id', auth()->user()->branch_id)
                ->where('id', $this->drawerId)
                ->first();

            if ($cashDrawer) {
                $cashDrawer->drawer_name = $this->drawer_name_input;
                $cashDrawer->drawer_code = $this->drawer_code_input;
                $cashDrawer->department_id = $this->selected_department_id;
                $cashDrawer->save();

                // Refresh the cash drawer list
                $this->fetchCashDrawerData();
                $this->dispatch('hideUpdateDrawerModal');
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Cash Drawer updated successfully!']);
                
            } else {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Cash Drawer not found.']);
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
