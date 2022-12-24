<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\ResourceService;

class ResourceController extends BaseApiController
{

    private $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    public function getAllCountries()
    {
        return $this->respond($this->resourceService->allCountries());
    }

    public function getTicketStatus2()
    {
        return $this->respond($this->resourceService->getTicketStatus2());
    }
}
