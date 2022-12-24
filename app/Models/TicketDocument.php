<?php

namespace App\Models;

use App\Helpers\Eloquent\Models\SolftDeletesDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDocument extends Model
{
    use HasFactory, SolftDeletesDateTime;

    protected $guarded = ['id'];

    protected $table = 'ticket_document';

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

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const DELETED_AT = 'deletedOn';

    const DOCPATH = '/documents';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deletedOn' => 'datetime',
        'isBackend' => 'boolean'
    ];
}
