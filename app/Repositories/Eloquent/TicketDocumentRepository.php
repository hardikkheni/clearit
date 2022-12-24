<?php

namespace App\Repositories\Eloquent;

use App\Models\TicketDocument;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;

class TicketDocumentRepository extends BaseRepository
{

    const MODEL_LABEL = 'Ticket Document';

    public function __construct(
        TicketDocument $model
    ) {
        parent::__construct($model);
    }

    public function create($data)
    {
        $data['isBackend'] = true;
        $data['createdBy'] = auth()->user()->id;
        return parent::create($data);;
    }

    public function getTicketAllDocumentByTicketId($ticketId, $desc)
    {
        return $this->find(['ticketId' => $ticketId, 'description' => $desc]);
    }

    public function getTicketDocumentListByTicketId($ticketId)
    {
        return $this->findAndOrSort(['ticketId' => $ticketId,])->withoutTrashed()->get();
    }

    public function softDelete($ticketDocOrId)
    {
        if (!is_object($ticketDocOrId)) {
            $ticketDocOrId = $this->findOrFail($ticketDocOrId);
        }
        $update = [
            'deletedOn' => Carbon::now(),
            'deletedBy' => auth()->user()->id
        ];
        $this->update([['id', $ticketDocOrId['id']]], $update);
        return $ticketDocOrId->refresh();
    }
}
