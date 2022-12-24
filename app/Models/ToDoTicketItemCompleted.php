<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoTicketItemCompleted extends Model
{
    use HasFactory;

    protected $table = 'to_do_ticket_item_completed';

    protected $guarded = ['id'];

    public $timestamps = false;
}
