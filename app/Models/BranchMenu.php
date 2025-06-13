<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchMenu extends Model
{
    //
    protected $table = 'branch_menus';
    protected $fillable = [
        'branch_id',
        'control_name',
        'is_available',
        'start_date',
        'end_date',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun',
        'created_by',
        'notes',
    ];


    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id')->where('recipe_type', 'Banquet');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function recipes()
    {
        return $this->hasMany(BranchMenuRecipe::class, 'branch_menu_id');
    }
}
