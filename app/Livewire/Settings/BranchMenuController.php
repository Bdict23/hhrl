<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Menu;
use App\Models\BranchMenuRecipe;
use App\Models\BranchMenu;

class BranchMenuController extends Component
{
    // displayed
    public $menuItems;
    public $menuControls = [];


    // for form table
    public $selectedRecipe = [];
    // forms
    public $mondaySelected = false;
    public $tuesdaySelected = false;
    public $wednesdaySelected = false;
    public $thursdaySelected = false;
    public $fridaySelected = false;
    public $saturdaySelected = false;
    public $sundaySelected = false;
    public $isAvailableInput = null;
    public $effectiveDateInput;
    public $endDateInput;
    public $controlNameInput;

    public function render()
    {
        return view('livewire.settings.branch-menu-controller');
    }

    public function mount(){
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->menuItems = Menu::with('category')
            ->where('company_id', auth()->user()->branch->company_id)->get();
            $this->menuControls = BranchMenu::where('branch_id', auth()->user()->branch_id)->get();

    }

    public function selectMenuItem($menuItemId)
    {
        $recipe = $this->menuItems->find($menuItemId);
        foreach($this->selectedRecipe as $selected) {
            if ($selected->id === $recipe->id) {
                 session()->flash('error','Item already selected.');
                return; // Prevent adding the same recipe again
            }
        }
        if ($recipe) {
            $this->selectedRecipe[] = $recipe;
        }
    }

    public function removeMenuItem($index)
    {
        if (isset($this->selectedRecipe[$index])) {
            unset($this->selectedRecipe[$index]);
            $this->selectedRecipe = array_values($this->selectedRecipe); // Re-index the array
        }
    }

    public function saveMenuControl()
    {
        if (count($this->selectedRecipe) > 0) {
            // $branch = auth()->user()->branch;
            // $branch->menuItems()->sync(collect($this->selectedRecipe)->pluck('id')->toArray());
            $branchMenu = new BranchMenu();
            $branchMenu->branch_id = auth()->user()->branch_id;
            $branchMenu->control_name = $this->controlNameInput;
            $branchMenu->start_date = $this->effectiveDateInput;
            $branchMenu->end_date = $this->endDateInput;
            $branchMenu->mon = $this->mondaySelected;
            $branchMenu->tue = $this->tuesdaySelected;
            $branchMenu->wed = $this->wednesdaySelected;
            $branchMenu->thu = $this->thursdaySelected;
            $branchMenu->fri = $this->fridaySelected;
            $branchMenu->sat = $this->saturdaySelected;
            $branchMenu->sun = $this->sundaySelected;
            $branchMenu->is_available = $this->isAvailableInput;
            $branchMenu->save();
            foreach ($this->selectedRecipe as $recipe) {
                BranchMenuRecipe::create([
                    'branch_menu_id' => $branchMenu->id,
                    'menu_id' => $recipe->id,
                ]);
            }
            session()->flash('success', 'Menu items saved successfully.');
        } else {
            session()->flash('error', 'No menu items selected.');
        }
        $this->fetchData();
    }

    public function deleteMenuControl($id)
    {
        $branchMenu = BranchMenu::find($id);
        if ($branchMenu) {
            $branchMenu->recipes()->delete(); // Delete associated recipes
            $branchMenu->delete();
            session()->flash('success', 'Menu control deleted successfully.');
        } else {
            session()->flash('error', 'Menu control not found.');
        }
        $this->fetchData();
    }
}
