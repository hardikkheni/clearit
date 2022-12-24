<?php

namespace App\Repositories\Eloquent;

use App\Models\TicketUserHts;

class TicketUserHtsRepository extends BaseRepository
{

    const MODEL_LABEL = 'Ticket User Hts';

    public function __construct(
        TicketUserHts $model
    ) {
        parent::__construct($model);
    }

    public function create($data)
    {
        $data['createdBy'] = auth()->user()->id;
        return parent::create($data);
    }
}
