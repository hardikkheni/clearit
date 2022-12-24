<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pga extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'pga';

    const CREATED_AT = 'createdOn';
}
