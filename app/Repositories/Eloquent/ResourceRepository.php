<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

class ResourceRepository extends BaseRepository
{
    public function __construct(
        Country $model
    ) {
        parent::__construct($model);
    }

    /**
     * Get all countries list
     * 
     * @return Collection
     */
    public function allCountires()
    {
        return $this->model->all();
    }
}
