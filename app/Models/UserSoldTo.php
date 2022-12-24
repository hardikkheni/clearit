<?php

namespace App\Models;

use App\Helpers\Eloquent\Models\SoftDeletesBoolean;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSoldTo extends Model
{
    use HasFactory, SoftDeletesBoolean;

    protected $guarded = ['id'];

    protected $table = 'user_sold_to';

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
    const UPDATED_AT = 'modifiedOn';

    const IS_DELETED = 'isDeleted';
}
