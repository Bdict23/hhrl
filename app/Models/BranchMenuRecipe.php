<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchMenuRecipe extends Model
{
    //
    protected $table = 'branch_menu_recipes';


    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
    public function branchMenu()
    {
        return $this->belongsTo(BranchMenu::class, 'branch_menu_id');
    }
}
