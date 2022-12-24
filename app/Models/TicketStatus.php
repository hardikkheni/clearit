<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'ticket_status';

    public $timestamps = false;

    public function toDoTicketItems()
    {
        return $this->belongsToMany(ToDoTicketItem::class, 'ticket_status_dependencies', 'ticketStatusId', 'toDoTicketItemId');
    }
}
