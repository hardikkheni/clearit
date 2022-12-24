<?php

namespace App\Repositories\Eloquent;

use App\Models\AffiliateApiCall;

class AffiliateApiCallRepository extends BaseRepository
{

    const MODEL_LABEL = 'Affiliate API call';

    public function __construct(
        AffiliateApiCall $model
    ) {
        parent::__construct($model);
    }
}
