<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class COATransactionTemplate extends Model
{
    //
    protected $table = 'actng_trans_templates';
     protected $fillable = [
        'company_id',
        'template_name',
         'description',
         'transaction_type',
         'module_type',
         'is_active',
         'created_by',
         'created_at',
         'updated_at',
     ];
        
     public function transactionDetails(){
        return $this->hasMany(COATransactionTemplateDetail::class, 'template_id');
     }
}
