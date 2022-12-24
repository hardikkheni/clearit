<?php

namespace App\Repositories\Eloquent;

use App\Models\DocumentUploadType;
use App\Models\TransportType;
use App\Repositories\Eloquent\BaseRepository;

class DocUploadTypeRepository extends BaseRepository
{

    const MODEL_LABEL = 'Document Upload Type';

    protected $tansportType;
    protected $tdRepo;

    public function __construct(
        DocumentUploadType $model,
        TransportType $tansportType,
        TicketDocumentRepository $tdRepo
    ) {
        parent::__construct($model);
        $this->tansportType = $tansportType;
        $this->tdRepo = $tdRepo;
    }

    public function modeOfTransportList()
    {
        return $this->tansportType->get();
    }

    public function getByMotId($id)
    {
        $mot = $this->tansportType->findOrFail($id);
        return DocumentUploadType::whereBelongsTo($mot, 'transportType')->get();
    }

    public function delete($id)
    {
        $dut = $this->findOrFail($id);
        $dut->delete();
        return $dut;
    }

    public function getDocumentUploadTypeListByConstant($const, $shipping_method)
    {
        $data = $this->call('getDocumentUploadTypeListByConstant', [$const, $shipping_method]);
        return @($data[0] ?? [[]])[0]->id;
    }

    public function getLatestTicketDocument($ticketId, $documentUploadTypeId)
    {
        $data = $this->call('getLatestTicketDocument', [$ticketId, $documentUploadTypeId]);
        return @($data[0] ?? [[]])[0];
    }

    public function getMissingDocumentUploadTypeListByTicket($ticket, $type = null)
    {
        $ticket = ($ticket ?? []);
        if (empty($ticket['id']) || empty($ticket['transport'])) {
            return [];
        }
        $where = ['is_required' => true, 'shipping_method' => $ticket['transport']];
        if ($type == 'affiliate') {
            $where['show_affiliate'] = true;
        } else if ($type == 'freight') {
            $where['show_freight_forwarder'] = true;
        }
        return $this->findAndOrSort($where)->whereNotIn('document_type', function ($query) use ($ticket) {
            $query->where(['ticketid' => $ticket['id']])->select(['description'])->from('ticket_document');
        })->pluck('document_type');
    }
}
