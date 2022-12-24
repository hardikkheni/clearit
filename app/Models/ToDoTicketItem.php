<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoTicketItem extends Model
{
    use HasFactory;

    protected $table = 'to_do_ticket_item';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'displayOrder' => 'integer'
    ];
}
