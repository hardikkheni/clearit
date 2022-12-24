<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\Agent\EditAgentRequest;
use App\Http\Requests\Api\Helper\Agent\InsertAgentRequest;
use App\Http\Requests\Api\Helper\Agent\ReOrderAgentStatusRequest;
use App\Http\Requests\Api\Helper\Agent\UpsertAgentStatusRequest;
use App\Services\Helper\AgentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AgentController extends BaseApiController
{

    private $agentService;

    public function __construct(
        AgentService $agentService
    ) {
        $this->agentService = $agentService;
    }

    public function create(InsertAgentRequest $request)
    {
        return $this->respond($this->agentService->create($request->validated()), HttpStatuses::HTTP_CREATED, 'Agent successfully created!.');
    }

    public function list(Request $request)
    {
        return $this->respond($this->agentService->dataTable($request->all()));
    }

    public function get($id)
    {
        try {
            $agent = $this->agentService->findOrFail($id);
            return $this->respond($agent);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Agent not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function edit($id, EditAgentRequest $request)
    {
        try {
            $agent = $this->agentService->edit($id, $request->validated());
            return $this->respond($agent, HttpStatuses::HTTP_OK, 'Agent successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Agent not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function allAgents()
    {
        return $this->respond($this->agentService->allAgents());
    }

    public function allInternalAgents()
    {
        return $this->respond($this->agentService->allInternalAgents());
    }

    public function allPermisisons()
    {
        return $this->respond($this->agentService->allPermissions());
    }

    public function savePermissions(Request $request)
    {
        try {
            $data = $request->all();
            $agentId = $data['agentId'];
            $permissions = $data['permissions'];
            return $this->respond($this->agentService->savePermissions($agentId, $permissions), HttpStatuses::HTTP_OK, "Agent Permissions successfully updated!.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Agent not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getAgentStatusList(Request $request)
    {
        return $this->respond($this->agentService->getAgentStatusList($request->all()));
    }

    public function upsertAgentStatus(UpsertAgentStatusRequest $request)
    {
        try {
            $data = $this->agentService->upsertAgentStatus($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "Agent Status successfully saved!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function deleteAgentStatus($id)
    {
        try {
            $data = $this->agentService->deleteAgentStatus($id);
            return $this->respond($data, HttpStatuses::HTTP_OK, "Agent Status successfully saved!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function reOrderAgentStatusList(ReOrderAgentStatusRequest $request)
    {
        try {
            $data = $this->agentService->reOrderAgentStatusList($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "Agent Status list successfully re-ordered!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }
}
