<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatusDependency extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'ticket_status_dependencies';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [];
}
