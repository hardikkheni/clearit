<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\AlertMessage\InsertAlertMessageRequest;
use App\Services\Helper\AlertMessageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AlertMessageController extends BaseApiController
{
    private $alertMessageService;

    public function __construct(AlertMessageService $alertMessageService)
    {
        $this->alertMessageService = $alertMessageService;
    }

    public function list(Request $request)
    {
        $data = $request->all();
        $data['select'] = ['subject', 'createdBy', 'createdOn', 'isActive', 'id'];
        return $this->respond($this->alertMessageService->dataTable($data));
    }

    public function create(InsertAlertMessageRequest $request)
    {
        return $this->respond($this->alertMessageService->create($request->validated()), HttpStatuses::HTTP_CREATED, 'Alert Message successfully created!.');
    }

    public function get($id)
    {
        try {
            $alertMessage = $this->alertMessageService->findOrFail($id);
            return $this->respond($alertMessage);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Alert Message not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function edit($id, InsertAlertMessageRequest $request)
    {
        try {
            $alertMessage = $this->alertMessageService->edit($id, $request->validated());
            return $this->respond($alertMessage, HttpStatuses::HTTP_OK, 'Alert Message successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Alert Message not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function delete($id)
    {
        try {
            $alertMessage = $this->alertMessageService->delete($id,);
            return $this->respond($alertMessage, HttpStatuses::HTTP_OK, 'Alert Message successfully deleted!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Alert Message not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }
}
