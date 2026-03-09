<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class COATransactionTemplateDetail extends Model
{
    //
    protected $table = 'actng_trans_template_details';
     protected $fillable = [
        'template_id',
        'account_title_id',
        'type',
        'created_at',
        'updated_at',
     ];

     public function accountTitle(){
        return $this->belongsTo(ChartOfAccount::class, 'account_title_id');
     }
}
