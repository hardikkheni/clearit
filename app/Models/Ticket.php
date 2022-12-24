<?php

namespace App\Models;

use App\Helpers\Eloquent\Models\SoftDeletesBoolean;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, SoftDeletesBoolean;

    const DOCUMENTPATH = '/ticket/';

    protected $guarded = ['id'];

    protected $table = 'ticket';

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

    const INVOICEPATH = '/invoice';
    const IADDITIONALPATH = '/iadditional';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isDeleted' => 'boolean',
        'isAccepted' => 'boolean',
        'isffiled' => 'boolean',
        'isVerified' => 'boolean',
        'isNew' => 'boolean',
        'isnewnote' => 'boolean',
        'requires_broker_review' => 'boolean',
        'requiresDelivery' => 'boolean',
        'requiresLiftGate' => 'boolean',
        'haveLoadingDock' => 'boolean',
        'disableEtaEmails' => 'boolean',
        'isPaidAdditional' => 'boolean'
    ];
}
