<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\TicketItem;
use App\Repositories\Eloquent\BaseRepository;

class TicketItemRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Ticket Item';

    public function __construct(
        TicketItem $model
    ) {
        parent::__construct($model);
    }
}
