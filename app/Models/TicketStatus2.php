<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus2 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'ticket_status2';

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ticketTransport' => 'boolean',
    ];
}
