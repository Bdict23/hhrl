<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_code',
        'company_tin',
        'company_type',
        'company_description',
    ];
    public function branches()

    {
        return $this->hasMany(Branch::class);
    }

    public function signatories()
    {
        return $this->hasMany(Signatory::class, 'company_id');
    }
    public function categories()
    {
        return $this->hasMany(Category::class, 'company_id');
    }
    public function classifications()
    {
        return $this->hasMany(Classification::class, 'company_id');
    }
    public function items()
    {
        return $this->hasMany(Item::class, 'company_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
