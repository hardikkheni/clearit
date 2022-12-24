<?php

namespace App\Repositories\Eloquent;

use App\Models\TicketStatusDependency;

class TicketStatusDependencyRepository extends BaseRepository
{

    const MODEL_LABEL = 'Ticket Status';

    public function __construct(TicketStatusDependency $model)
    {
        parent::__construct($model);
    }
}
