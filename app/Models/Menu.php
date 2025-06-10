<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Signatory;
use App\Models\Recipe;
use App\Models\PriceLevel;
use App\Models\Employee;

class Menu extends Model
{

    use HasFactory;
    protected $table = 'menus';
    protected $fillable = [
        'menu_name',
        'menu_image',
        'category_id',
        'status',
        'created_by',
        'reviewer_id',
        'approver_id',
        'company_id'
    ];


    public function categories(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function approver(){
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    public function reviewer(){
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function preparer(){
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function recipes(){
        return $this->hasMany(Recipe::class, 'menu_id');
    }

    public function mySRP(){
        return $this->hasOne(PriceLevel::class, 'menu_id')->where([['branch_id', auth()->user()->branch_id], ['price_type', 'SRP']])
            ->latest('created_at');
    }

    public function price_levels(){
        return $this->hasMany(PriceLevel::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


}
