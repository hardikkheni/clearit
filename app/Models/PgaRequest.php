<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PgaRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'pga_request';

    const CREATED_AT = 'createdOn';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isSigned' => 'boolean',
    ];
}
