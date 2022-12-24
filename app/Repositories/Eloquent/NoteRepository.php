<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasGuidColumn;
use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Helpers\Eloquent\Columns\HasUserColumn;
use App\Models\Note;
use Illuminate\Support\Str;

class NoteRepository extends BaseRepository
{

    use HasGuidColumn, HasUserColumn, HasTicketColumn;

    const MODEL_LABEL = 'Note';

    public function __construct(
        Note $model
    ) {
        parent::__construct($model);
    }

    public function create($data)
    {
        $data['guid'] = Str::upper(Str::uuid());
        $data['userId'] = auth()->user()->id;
        return parent::create($data);
    }
}
