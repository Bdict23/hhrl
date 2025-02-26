<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\category;
use App\Models\signatories;
use App\Models\recipe;
use App\Models\priceLevel;
use App\Models\employees;

class menu extends Model
{
    use HasFactory;
    protected $table = 'menus';


    public function categories(){
        return $this->belongsTo(category::class, 'category_id');
    }

    public function approver(){
        return $this->belongsTo(employees::class, 'approver_id');
    }

    public function reviewer(){
        return $this->belongsTo(employees::class, 'reviewer_id');
    }

    public function preparer(){
        return $this->belongsTo(employees::class, 'created_by');
    }



    public function recipes(){
        return $this->hasMany(recipe::class, 'menu_id');
    }

    public function price_levels(){
        return $this->hasMany(priceLevel::class);
    }


}
