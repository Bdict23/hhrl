<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Denomination extends Model
{
    //
    use HasFactory;
    protected $table = 'denominations';
    protected $fillable = [
        'type',
        'value',
    ];
}
