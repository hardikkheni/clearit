<?php

namespace App\Repositories\Eloquent;

use App\Models\Pga;

class PgaRepository extends BaseRepository
{

    const MODEL_LABEL = 'Pga';

    public function __construct(
        Pga $model
    ) {
        parent::__construct($model);
    }
}
