<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\TicketInvoice;
use App\Repositories\Eloquent\BaseRepository;

class TicketInvoiceRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Ticket Invoice';

    public function __construct(
        TicketInvoice $model
    ) {
        parent::__construct($model);
    }
}
