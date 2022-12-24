<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnnualBondDocument extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'user_annual_bond_document';

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isSigned' => 'boolean',
    ];
}
