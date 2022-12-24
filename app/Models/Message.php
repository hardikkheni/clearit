<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'message';

    const ATTACHMENTPATH = '/message/';

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

    protected $appends = ['file_url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isMaster' => 'boolean',
        'isNew' => 'boolean'
    ];

    public function getFileUrlAttribute()
    {
        return $this->messageFile ? Storage::disk('storage')->url(self::ATTACHMENTPATH  . $this->messageFile) : null;
    }
}
