<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    //

    protected $table = 'company_audits';

    protected $fillable = [
        'company_id',
        'created_by',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
