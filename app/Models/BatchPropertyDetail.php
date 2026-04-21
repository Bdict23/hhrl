<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchPropertyDetail extends Model
{
    protected $table = 'batch_property_details';
     protected $fillable = [
         'batch_id',
         'code',
         'item_id',
         'branch_id',
         'serial',
         'sidr_no',
         'cost',
         'lifespan',
         'span_ended',
         'condition',
         'created_at',
         'updated_at',
     ];

     public function itemDetail(){
        return $this->belongsTo(Item::class , 'item_id' );
     }

     public function batch(){
        return $this->belongsTo(BatchProperty::class ,'batch_id');
     }
}
