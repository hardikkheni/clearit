<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Http\Requests\Api\TicketUserHts\CreateTicketUserHtsRequest;
use App\Http\Requests\Api\TicketUserHts\UpdateTicketUserHtsRequest;
use App\Services\TicketUserHtsService;

class TicketUserHtsController extends BaseApiController
{

    protected TicketUserHtsService $tuhService;

    public function __construct(
        TicketUserHtsService $tuhService
    ) {
        $this->tuhService = $tuhService;
    }

    public function create(CreateTicketUserHtsRequest $request)
    {
        $data = $this->tuhService->create($request->validated());
        return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket User HTS successfully created!.");
    }

    public function update($id, UpdateTicketUserHtsRequest $request)
    {
        $data = $this->tuhService->update($id, $request->validated());
        return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket User HTS successfully updated!.");
    }

    public function delete($id)
    {
        $data = $this->tuhService->delete($id);
        return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket User HTS successfully deleted!.");
    }
}
