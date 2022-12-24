<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Note extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'note';

    const ATTACHMENTPATH = '/note/';

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

    protected $appends = ['file_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function getFileUrlAttribute()
    {
        return $this->notefile ? Storage::disk('storage')->url(self::ATTACHMENTPATH  . $this->notefile) : null;
    }
}
