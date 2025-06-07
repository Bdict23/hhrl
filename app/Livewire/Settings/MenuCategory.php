<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Category as MenuCategoryModel;
class MenuCategory extends Component
{
    public $menuCategories = [];

    // input properties for the form
    public $menu_category_name_input;
    public $menu_category_description_input;
    public $menu_category_id;


    public function render()
    {
        return view('livewire.settings.menu-category');
    }

    public function mount()
    {
        // Initialization logic can go here if needed
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->menuCategories = MenuCategoryModel::where('category_type', 'menu')->where('company_id', auth()->user()->branch->company_id)->where('status', 'ACTIVE')->get();
        // dd($this->menuCategories);
    }

    public function storeMenuCategory()
    {
        $this->validate([
            'menu_category_name_input' => 'required|string|max:255',
            'menu_category_description_input' => 'nullable|string|max:100',
        ]);

        MenuCategoryModel::create([
            'category_name' => $this->menu_category_name_input,
            'category_description' => $this->menu_category_description_input,
            'category_type' => 'menu',
            'company_id' => auth()->user()->branch->company_id,
            'created_by' => auth()->user()->emp_id,
        ]);

        $this->reset();
        $this->fetchData();
        session()->flash('success', 'Menu category created successfully.');
        $this->dispatch('clearMenuCategoryForm');
    }

    public function deactivateMenuCategory($id)
    {
        $menuCategory = MenuCategoryModel::find($id);
        if ($menuCategory) {
            $menuCategory->status = 'INACTIVE';
            $menuCategory->save();
            $this->reset();
            $this->fetchData();
            session()->flash('success', 'Menu category deactivated successfully.');
        } else {
            session()->flash('error', 'Menu category not found.');
        }
    }
    public function editMenuCategory($id)
    {
        $menuCategory = MenuCategoryModel::find($id);
        if ($menuCategory) {
            $this->menu_category_id = $menuCategory->id;
            $this->menu_category_name_input = $menuCategory->category_name;
            $this->menu_category_description_input = $menuCategory->category_description;
            $this->dispatch('editMenuCategory', ['id' => $id]);
        } else {
            session()->flash('error', 'Menu category not found.');
        }
    }

    public function updateMenuCategory()
    {
        $this->validate([
            'menu_category_name_input' => 'required|string|max:255',
            'menu_category_description_input' => 'nullable|string|max:100',
        ]);

        $menuCategory = MenuCategoryModel::find($this->menu_category_id);
        if ($menuCategory) {
            $menuCategory->category_name = $this->menu_category_name_input;
            $menuCategory->category_description = $this->menu_category_description_input;
            $menuCategory->updated_by = auth()->user()->emp_id;
            $menuCategory->save();

            $this->reset();
            $this->fetchData();
            session()->flash('success', 'Menu category updated successfully.');
            $this->dispatch('closeEditMenuCategoryModal');
        } else {
            session()->flash('error', 'Menu category not found.');
        }
    }

}
