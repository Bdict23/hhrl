<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentRequestAttachment extends Model
{
    //
    protected $table = 'equipment_request_attachments';
    protected $fillable = [
        'equipment_request_id',
        'file_path',
    ];

    public function equipmentRequest()
    {
        return $this->belongsTo(EquipmentRequest::class, 'equipment_request_id');
    }
}
