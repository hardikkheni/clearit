<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightosInvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'freightos_invoice_item';

    public $timestamps = false;
}
