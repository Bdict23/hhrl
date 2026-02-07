<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftDenomination extends Model
{
    //
    use HasFactory;
    protected $table = 'shift_denominations';
    protected $fillable = [
        'shift_id',
        'denomination_id',
        'amount',
        'quantity',
        'counter_type',
    ];

    public function denomination()
    {
        return $this->belongsTo(Denomination::class, 'denomination_id');
    }
}
