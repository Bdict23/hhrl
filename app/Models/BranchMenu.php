<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchMenu extends Model
{
    //
    protected $table = 'branch_menus';
    protected $fillable = [
        'branch_id',
        'menu_id',
        'price_level_id',
        'status',
        'created_by',
        'approved_by',
        'reviewed_by',
        'notes',
        'combo_recipe_id',
    ];


    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id')->where('recipe_type', 'Banquet');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
