<?php

namespace App\Models;

use App\Helpers\Eloquent\Models\SoftDeletesBoolean;
use App\Helpers\Eloquent\Scopes\HasActiveScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasActiveScope, SoftDeletesBoolean;

    protected $guarded = ['id'];

    protected $table = 'user';


    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'CreatedOn';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'ModifiedOn';
    const IS_DELETED = 'isDeleted';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isAgent' => 'boolean',
        'isMaster' => 'boolean',
        'isActive' => 'boolean',
        'displayInternally' => 'boolean',
        'isBackend' => 'boolean',
        'isCustomer' => 'boolean',
        'isVerified' => 'boolean',
        'isBeta' => 'boolean',
        'isCertificate' => 'boolean',
        'isSigned' => 'boolean',
        'isDeleted' => 'boolean'
    ];


    public function scopeAgent($query)
    {
        return $query->where('isAgent', true);
    }

    public function scopeDisplayInternally($query)
    {
        return $query->where('displayInternally', true);
    }
}
