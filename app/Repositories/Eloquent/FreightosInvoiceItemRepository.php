<?php

namespace App\Repositories\Eloquent;

use App\Models\FreightosInvoiceItem;
use App\Repositories\Eloquent\BaseRepository;

class FreightosInvoiceItemRepository extends BaseRepository
{

    const MODEL_LABEL = 'Freight Invoice Item';

    public function __construct(FreightosInvoiceItem $model)
    {
        parent::__construct($model);
    }

    public function getFreightInvoiceItemList($ticketId)
    {
        return $this->model->join('freightos_charges as fc', 'fc.freightOSInvoiceItemId', 'freightos_invoice_item.id')->select(['*', 'fc.id as fcid', 'fc.amount', 'fc.ticketId'])->where('fc.ticketId', $ticketId)->get();
    }

    public function deleteFreightInvoiceItem($id)
    {
    }
}
