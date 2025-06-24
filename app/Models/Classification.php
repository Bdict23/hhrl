<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    //
    protected $table = 'classifications';

    protected $fillable = [
        'name',
        'classification_name',
        'classification_description',
        'category_id',
        'class_parent',
        'company_id'
    ];

    public function sub_classifications()
    {
        return $this->hasMany(Classification::class, 'class_parent');
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class, 'class_parent');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
