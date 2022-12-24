<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Http\Requests\Api\TicketDocument\CreateTicketDocumentRequest;
use App\Http\Requests\Api\TicketDocument\UpdateTicketDocumentRequest;
use App\Services\TicketDocumentService;
use Illuminate\Http\Request;

class TicketDocumentController extends BaseApiController
{

    protected TicketDocumentService $ticketDocService;

    function __construct(
        TicketDocumentService $ticketDocService
    ) {
        $this->ticketDocService = $ticketDocService;
    }

    public function delete($id)
    {
        $data = $this->ticketDocService->delete($id);
        return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket Document successfully deleted!.");
    }

    public function updateDocUploadType($id, UpdateTicketDocumentRequest $request)
    {
        $data = $this->ticketDocService->updateDocUploadType($id, $request->validated());
        return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket Document successfully updated!.");
    }

    public function create(CreateTicketDocumentRequest $request)
    {
        $data = $this->ticketDocService->create($request->validated());
        return $this->respond($data, HttpStatuses::HTTP_CREATED, "Ticket Document successfully created!.");
    }
}
