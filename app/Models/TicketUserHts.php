<?php

namespace App\Models;

use App\Helpers\Eloquent\Models\SolftDeletesDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketUserHts extends Model
{
    use HasFactory, SolftDeletesDateTime;

    protected $table = 'ticketUserHTS';
    protected $guarded = ['id'];

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
    const UPDATED_AT = null;

    const DELETED_AT = 'deletedOn';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isVisible' => 'boolean'
    ];
}
