<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightosCharge extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'freightos_charges';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdOn';
}
