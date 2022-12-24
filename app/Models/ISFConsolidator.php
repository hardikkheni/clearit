<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISFConsolidator extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'ISFConsolidator';

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

    public function contacts()
    {
        return $this->hasMany(ISFConsolidatorContact::class, 'isfConsolidatorId');
    }
}
