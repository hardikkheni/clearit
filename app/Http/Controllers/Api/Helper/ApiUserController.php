<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\ApiUser\EditApiUserRequest;
use App\Http\Requests\Api\Helper\ApiUser\InsertApiUserRequest;
use App\Services\Helper\ApiUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ApiUserController extends BaseApiController
{
    private $apiUserService;

    public function __construct(ApiUserService $apiUserService)
    {
        $this->apiUserService = $apiUserService;
    }

    public function list(Request $request)
    {
        return $this->respond($this->apiUserService->dataTable($request->all()));
    }

    public function create(InsertApiUserRequest $request)
    {
        return $this->respond($this->apiUserService->create($request->validated()), HttpStatuses::HTTP_CREATED, 'Api User successfully created!.');
    }

    public function get($id)
    {
        try {
            $agent = $this->apiUserService->findOrFail($id);
            return $this->respond($agent);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Api User not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function edit($id, EditApiUserRequest $request)
    {
        try {
            $agent = $this->apiUserService->edit($id, $request->validated());
            return $this->respond($agent, HttpStatuses::HTTP_OK, 'Agent successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Agent not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }
}
