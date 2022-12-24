<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'reminder';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdOn';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modifiedOn';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
}
