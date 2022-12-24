<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\FeightForwarder\EditFeightForwarderRequest;
use App\Http\Requests\Api\Helper\FeightForwarder\InsertFeightForwarderRequest;
use App\Http\Requests\Api\Helper\FeightForwarder\InsertFFContactRequest;
use App\Services\Helper\FeightForwarderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FeightForwarderController extends BaseApiController
{

    private $feightForwarderService;

    public function __construct(
        FeightForwarderService $feightForwarderService
    ) {
        $this->feightForwarderService = $feightForwarderService;
    }

    public function list(Request $request)
    {
        return $this->respond($this->feightForwarderService->dataTable($request->all()));
    }

    public function create(InsertFeightForwarderRequest $request)
    {
        return $this->respond($this->feightForwarderService->create($request->validated()), HttpStatuses::HTTP_CREATED, 'Feight Forwarder successfully created!.');
    }

    public function get($id)
    {
        try {
            $ff = $this->feightForwarderService->findOrFail($id);
            return $this->respond($ff);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Forwarder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function edit($id, EditFeightForwarderRequest $request)
    {
        try {
            $data = $request->validated();
            $ff = $this->feightForwarderService->edit($id, $data);
            return $this->respond($ff, HttpStatuses::HTTP_OK, 'Feight Forwarder successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Forwarder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function delete($id)
    {
        try {
            $ff = $this->feightForwarderService->delete($id,);
            return $this->respond($ff, HttpStatuses::HTTP_OK, 'Feight Forwarder successfully deleted!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Forwarder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function createContact(InsertFFContactRequest $request, $ffId)
    {
        try {
            return $this->respond($this->feightForwarderService->createContact($ffId, $request->validated()), HttpStatuses::HTTP_CREATED, 'Feight Forwarder Contact successfully created!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Forwarder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function getContact($id)
    {
        try {
            $fc = $this->feightForwarderService->findContactOrFail($id);
            return $this->respond($fc);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Contact not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function editContact($ffId, $id, InsertFFContactRequest $request)
    {
        try {
            $data = $request->validated();
            $ff = $this->feightForwarderService->editContact($id, $data);
            return $this->respond($ff, HttpStatuses::HTTP_OK, 'Feight Contact successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Contact not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function deleteContact($id)
    {
        try {
            $fc = $this->feightForwarderService->deleteContact($id,);
            return $this->respond($fc, HttpStatuses::HTTP_OK, 'Feight Contact successfully deleted!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Contact not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getContacts($id)
    {
        try {
            $fc = $this->feightForwarderService->getContacts($id);
            return $this->respond($fc, HttpStatuses::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Feight Contact not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }
}
