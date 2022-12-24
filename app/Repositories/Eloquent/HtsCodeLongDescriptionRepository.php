<?php

namespace App\Repositories\Eloquent;

use App\Models\HtsCodeLongDescription;

class HtsCodeLongDescriptionRepository extends BaseRepository
{

    const MODEL_LABEL = 'Hts Code Long Description';

    public function __construct(
        HtsCodeLongDescription $model
    ) {
        parent::__construct($model);
    }

    public function upsertByHtsCode($data)
    {
        if (!$data['htsCode']) {
            return parent::create($data);
        } else if (parent::find([['htsCode', $data['htsCode']]])) {
            return parent::update([['htsCode', $data['htsCode']]], $data);
        }
        return null;
    }
}
