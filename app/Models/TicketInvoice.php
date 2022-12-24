<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketInvoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'ticket_invoice';

    public $timestamps = false;
}
