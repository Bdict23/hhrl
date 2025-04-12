<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'supp_name',
        'postal_address',
        'contact_no_1',
        'supp_address',
        'contact_no_2',
        'tin_number',
        'contact_person',
        'input_tax',
        'supplier_code',
        'email',
        'description',
        'supplier_status'
    ];

    public function priceLevels()
    {
        return $this->hasMany(PriceLevel::class);
    }
}
