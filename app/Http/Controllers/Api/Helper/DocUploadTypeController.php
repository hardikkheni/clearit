<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\DocUploadType\UpsertDocUploadTypeRequest;
use App\Services\Helper\DocUploadTypeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DocUploadTypeController extends BaseApiController
{
    private $docUploadTypeService;

    public function __construct(DocUploadTypeService $docUploadTypeService)
    {
        $this->docUploadTypeService = $docUploadTypeService;
    }

    public function modeOfTransportList()
    {
        return $this->respond($this->docUploadTypeService->modeOfTransportList());
    }

    public function getByMotId($modeId)
    {
        try {
            return $this->respond($this->docUploadTypeService->getByMotId($modeId));
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Transport Type Not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function upsert(UpsertDocUploadTypeRequest $request)
    {
        return $this->respond($this->docUploadTypeService->upsert($request->validated()), HttpStatuses::HTTP_CREATED, 'Document Upload Type successfully saved!.');
    }

    public function delete($id)
    {
        try {
            $docUploadType = $this->docUploadTypeService->delete($id,);
            return $this->respond($docUploadType, HttpStatuses::HTTP_OK, 'Document Upload Type successfully deleted!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Document Upload Type not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }
}
