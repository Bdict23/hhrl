<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\menu;

class order_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'qty',
    ];

    public function menu()
    {
        return $this->belongsTo(menu::class, 'menu_id');
    }
}
