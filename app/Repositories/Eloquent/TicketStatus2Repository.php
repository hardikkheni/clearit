<?php

namespace App\Repositories\Eloquent;

use App\Models\TicketStatus2;

class TicketStatus2Repository extends BaseRepository
{

    const MODEL_LABEL = 'Ticket Status';

    public function __construct(TicketStatus2 $model)
    {
        parent::__construct($model);
    }

    public function getAllStatus()
    {
        return $this->model->get();
    }
}
