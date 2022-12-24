<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertMessage extends Model
{
    use HasFactory;

    // protected $table = 'alert_messages';

    protected $guarded = ['id'];

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
    // const DELETED_AT = 'deletedOn';

    protected $casts = [
        'isActive' => 'boolean',
        'acknowledgementRequired' => 'boolean',
        'showNewAgent' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }
}
