<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentUploadType extends Model
{
    use HasFactory;

    protected $table = 'document_upload_type';

    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
        'show_customer' => 'boolean',
        'show_affiliate' => 'boolean',
        'show_freight_forwarder' => 'boolean'
    ];

    public $timestamps = false;

    public function transportType()
    {
        return $this->belongsTo(DocumentUploadType::class, 'shipping_method');
    }
}
