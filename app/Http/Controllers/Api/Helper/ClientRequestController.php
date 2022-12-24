<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\ClientRequest\BulkInsertClientReqRequest;
use App\Services\Helper\ClientRequestService;
use Illuminate\Http\Request;

class ClientRequestController extends BaseApiController
{

    protected $clientReqService;

    public function __construct(
        ClientRequestService $clientReqService
    ) {
        $this->clientReqService = $clientReqService;
    }

    public function list()
    {
        $list = $this->clientReqService->list();
        $count = count($list);
        return $this->respond(['list' => $list, 'count' => $count]);
    }

    public function markViewed($id)
    {
        $this->clientReqService->markViewed($id);
        return $this->respond(null, HttpStatuses::HTTP_OK, "Notifications marked viewed successfully!.");
    }

    public function dailyMails(Request $request)
    {
        return $this->respond($this->clientReqService->dailyMails($request->user()->id));
    }

    public function bulkInsert(BulkInsertClientReqRequest $request)
    {
        $data = $this->clientReqService->bulkInsert($request->validated());
        return $this->respond($data, HttpStatuses::HTTP_CREATED, "Client Request inserted successfully!");
    }

    public function markAsReceived($id)
    {
        $data = $this->clientReqService->markAsReceived($id);
        return $this->respond($data, HttpStatuses::HTTP_OK, "Client Request mark as received successfully!");
    }
}
