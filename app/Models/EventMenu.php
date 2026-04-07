<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMenu extends Model
{
    //
    protected $table = 'event_menus';
    protected $fillable = [
        'event_id',
        'menu_id',
        'note',
        'qty',
        'price_id',
        'total_amount',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function price()
    {
        return $this->belongsTo(PriceLevel::class, 'price_id');
    }

    public function discounts()
    {
        return $this->hasMany(EventDiscount::class, 'event_menu_id');
    }
}
