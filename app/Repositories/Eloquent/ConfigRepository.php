<?php

namespace App\Repositories\Eloquent;

use App\Models\Config;
use Illuminate\Database\Eloquent\Collection;

class ConfigRepository extends BaseRepository
{
    const MODEL_LABEL = 'Config';

    public function __construct(
        Config $model
    ) {
        parent::__construct($model);
    }

    /**
     * Get all countries list
     *
     * @return Collection
     */
}
