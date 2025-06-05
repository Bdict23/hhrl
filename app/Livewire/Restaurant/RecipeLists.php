<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\Menu; // Assuming Menu model exists in App\Models namespace

class RecipeLists extends Component
{
    public $recipes = [];
    public $recipe_id;

    public function render()
    {
        return view('livewire.restaurant.recipe-lists');
    }

    public function mount()
    {
       $this->fetchData();
    }

    public function fetchData()
    {
        $this->recipes = Menu::with('categories')
            ->where('company_id', auth()->user()->branch->company_id)
            ->get();
    }
}
