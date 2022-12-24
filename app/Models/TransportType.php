<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportType extends Model
{
    use HasFactory;

    protected $table = 'transportTypes';

    protected $guarded = ['id'];

    protected $casts = [];

    public $timestamps = false;

    public function docUploadTypes()
    {
        return $this->hasMany(DocumentUploadType::class, 'shipping_method');
    }
}
