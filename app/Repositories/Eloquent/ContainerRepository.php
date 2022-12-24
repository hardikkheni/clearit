<?php

namespace App\Repositories\Eloquent;

use App\Models\Container;
use App\Repositories\Eloquent\BaseRepository;

class ContainerRepository extends BaseRepository
{

    const MODEL_LABEL = 'Container';

    public function __construct(
        Container $model
    ) {
        parent::__construct($model);
    }

    public function getContainerTraking($containerId)
    {
        $data = $this->call('getContainerTraking', [$containerId]);
        return @$data[0][0];
    }
}
