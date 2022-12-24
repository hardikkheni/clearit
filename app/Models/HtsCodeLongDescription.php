<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HtsCodeLongDescription extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'hts_code_long_descriptions';

    public $timestamps = false;
}
