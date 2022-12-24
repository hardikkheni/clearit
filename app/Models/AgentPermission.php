<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentPermission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'agent_permission';

    public $timestamps = false;
}
