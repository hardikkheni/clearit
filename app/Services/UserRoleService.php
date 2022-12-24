<?php

namespace App\Services;

use App\Repositories\Eloquent\UserRoleRepository;

class UserRoleService
{
    protected $userRoleRepo;

    public function __construct(
        UserRoleRepository $userRoleRepo
    ) {
        $this->userRoleRepo = $userRoleRepo;
    }

    public function getUserRoles()
    {
        return $this->userRoleRepo->active();
    }

    public function getUserRoleAgents($roleId)
    {
        return $this->userRoleRepo->getRoleAgents($roleId);
    }

    public function updateUserRolePermissions($data)
    {
        $role = $this->userRoleRepo->findOrFail($data['role']);
        $role->fill(['bitmaskValue' => array_sum($data['permissions'] ?? [])]);
        $role->save();
        return $role;
    }

    public function grantRevokeAgentFromRole($data)
    {
        return $this->userRoleRepo->saveAgentRole(@$data['agentId'], @$data['roleId']);
    }
}
