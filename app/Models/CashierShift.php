<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashierShift extends Model
{
    //
    use HasFactory;
    protected $table = 'cashier_shifts';
    protected $fillable = [
        'reference',
        'cashier_id',
        'branch_id',
        'drawer_id',
        'shift_status',
        'shift_started',
        'shift_ended',
        'starting_cash',
        'ending_cash',
        'total_sales',
        'discrepancy_status',
        'notes',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'cashier_id');
    }
    public function cashDrawer()
    {
        return $this->belongsTo(CashDrawer::class, 'drawer_id');
    }
    public function shiftDenominations()
    {
        return $this->hasMany(ShiftDenomination::class, 'shift_id');
    }

    public function closingShiftDenominations()
    {
        return $this->hasMany(ShiftDenomination::class, 'shift_id')->where('counter_type', 'ENDING_CASH');
    }
    public function openingShiftDenominations()
    {
        return $this->hasMany(ShiftDenomination::class, 'shift_id')->where('counter_type', 'STARTING_CASH') ?? 0;
    }
    public function payments(){
         return $this->hasMany(Payment::class, 'shift_id');
    }
   
}
