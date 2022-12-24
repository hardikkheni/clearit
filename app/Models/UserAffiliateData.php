<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAffiliateData extends Model
{
    use HasFactory;

    protected $table = 'user_affiliate_data';

    protected $guarded = ['id'];

    const FILESPATH = '/affiliate/tmp/';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdOn';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;
}
