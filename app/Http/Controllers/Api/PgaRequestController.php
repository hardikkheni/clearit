<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Services\PgaRequestService;
use Illuminate\Http\Request;

class PgaRequestController extends BaseApiController
{
    protected $pgaReqService;

    public function __construct(
        PgaRequestService $pgaReqService
    ) {
        $this->pgaReqService = $pgaReqService;
    }

    public function delete($id)
    {
        $data = $this->pgaReqService->delete($id);
        return $this->respond($data, HttpStatuses::HTTP_OK, "Pga Request successfully deleted!.");
    }
}
