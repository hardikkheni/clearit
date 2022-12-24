<?php

namespace App\Services\Helper;

use App\Repositories\Eloquent\AgentRepository;
use App\Repositories\Eloquent\AgentStatusTypeRepository;
use Illuminate\Validation\ValidationException;

class AgentService
{

    protected $agentRepo;
    protected $agentStatusTypeRepo;

    public function __construct(
        AgentRepository $agentRepo,
        AgentStatusTypeRepository $agentStatusTypeRepo
    ) {
        $this->agentRepo = $agentRepo;
        $this->agentStatusTypeRepo = $agentStatusTypeRepo;
    }

    public function create($data)
    {
        $exist = $this->agentRepo->findByEmail($data['email']);
        if ($exist) {
            throw ValidationException::withMessages([
                'email' => ['Email already exist!.']
            ]);
        }
        if (@$data['permissions']) {
            $data['roleBitmask'] = array_sum($data['permissions'] ?? []);
            unset($data['permissions']);
        }
        return $this->agentRepo->create($data);
    }

    public function dataTable($data)
    {
        return $this->agentRepo->dataTable($data);
    }

    public function findOrFail($id)
    {
        return $this->agentRepo->findOrFail($id);
    }

    public function edit($id, $data)
    {
        $agent = $this->agentRepo->findOrFail($id);
        $exist = $this->agentRepo->findByEmail($data['email']);
        if ($exist && $exist->id != $agent->id) {
            throw ValidationException::withMessages([
                'email' => ['Email already exist!.']
            ]);
        }
        if (@$data['permissions']) {
            $data['roleBitmask'] = array_sum($data['permissions'] ?? []);
            unset($data['permissions']);
        }
        $userToSave['isAgent'] = true;
        $userToSave['isBackend'] = true;
        $userToSave['isCustomer'] = false;
        return $this->agentRepo->edit($id, $data);
    }

    public function allAgents()
    {
        return $this->agentRepo->allAgents();
    }

    public function allInternalAgents()
    {
        return $this->agentRepo->allInternalAgents();
    }

    public function allPermissions()
    {
        return $this->agentRepo->allPermissions();
    }

    public function savePermissions($id, $permissions)
    {
        return $this->agentRepo->savePermissions($id, $permissions);
    }

    public function getAgentStatusList($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $role = @$data['role'];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            $type = $ticketConstants['type']['CLEARANCE'];
        }
        $where = [['ticketType', '=', $type]];
        $sorts = [['displayOrder', 'ASC']];
        if ($role) {
            $where[] = ['userRoleId', '=', $role];
        } else {
            $where[] = ['userRoleId', null];
        }
        return $this->agentStatusTypeRepo->find($where, $sorts);
    }

    public function upsertAgentStatus($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $role = $data['role'] ?? null;
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if (!$role) {
            throw ValidationException::withMessages([
                'status' => ['Invalid ticket user role']
            ]);
        }
        if (@$data['displayOrder']) {
            $this->agentStatusTypeRepo->shiftFromDisplayOrder($data['displayOrder']);
        }
        return $this->agentStatusTypeRepo->upsert(['id' => @$data['id']], [
            'statusName' => $data['statusName'],
            'ticketType' => $type,
            'userRoleId' => $role,
            'displayOrder' => @$data['displayOrder']
        ]);
    }

    public function deleteAgentStatus($id)
    {
        return $this->agentStatusTypeRepo->delete($id);
    }

    public function reOrderAgentStatusList($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $role = $data['role'] ?? null;
        $list = $data['list'] ?? [];
        $newList = [];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if (!$role) {
            throw ValidationException::withMessages([
                'status' => ['Invalid ticket user role']
            ]);
        }
        foreach ($list as $key => $todo) {
            $newList[] = $this->agentStatusTypeRepo->upsert(['id' => @$todo['id']], [
                'statusName' => $todo['statusName'],
                'ticketType' => $type,
                'userRoleId' => $role,
                'displayOrder' => $key + 1
            ]);
        }
        return $newList;
    }
}
