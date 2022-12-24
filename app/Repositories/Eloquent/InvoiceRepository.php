<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\Invoice;
use App\Repositories\Eloquent\BaseRepository;

class InvoiceRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Invoice';

    public function __construct(
        Invoice $model
    ) {
        parent::__construct($model);
    }
}
