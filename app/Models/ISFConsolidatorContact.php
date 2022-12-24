<?php

namespace App\Models;

use App\Helpers\Eloquent\Models\SolftDeletesDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISFConsolidatorContact extends Model
{
    use HasFactory, SolftDeletesDateTime;

    protected $guarded = ['id'];

    protected $table = 'ISFConsolidatorContact';

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

    /**
     * The name of the "deleted at" column.
     * 
     * @var string
     */
    const DELETED_AT = 'deletedOn';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isDefault' => 'boolean',
    ];
}
