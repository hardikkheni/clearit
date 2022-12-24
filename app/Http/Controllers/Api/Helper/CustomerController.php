<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Helper\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CustomerController extends BaseApiController
{
    private $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    public function list(Request $request)
    {
        return $this->respond($this->userService->dataTable($request->all()));
    }
    public function unverifiedWithPoaDataTable(Request $request)
    {
        $data = $request->all();
        $response = $this->userService->unverifiedWithPoaDataTable($data);
        return $this->respond($response);
    }
}
