<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    use HasFactory;

    const LOGOPATH = '/affiliate/logos';

    protected $table = 'customer';

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
    // const DELETED_AT = 'removedAt';

    protected $casts = [
        'isActive' => 'boolean',
        'expressEnabled' => 'boolean',
        'isDeleted' => 'boolean',
        'isPaymentProfileRequired' => 'boolean',
        'isPaymentProfileRequired' => 'boolean',
        'isCreditAccount' => 'boolean',
        'disableChatEmails' => 'boolean',
    ];

    protected $appends = [
        'logo_url'
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logofilename ? Storage::url(self::LOGOPATH . '/' . $this->logofilename) : null;
    }
}
