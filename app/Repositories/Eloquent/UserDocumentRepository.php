<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasUserColumn;
use App\Models\UserDocument;

class UserDocumentRepository extends BaseRepository
{

    use HasUserColumn;

    const MODEL_LABEL = 'User Document';

    public function __construct(
        UserDocument $model
    ) {
        parent::__construct($model);
    }
}
