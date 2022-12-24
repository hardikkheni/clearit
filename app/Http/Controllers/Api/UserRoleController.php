<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\UserRole\GrantRevokeAgentRoleRequest;
use App\Http\Requests\Api\UserRole\UpdateUserRolePermissionsRequest;
use App\Services\UserRoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRoleController extends BaseApiController
{
    protected $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function getUserRoles()
    {
        return $this->respond($this->userRoleService->getUserRoles());
    }

    public function getUserRoleAgents($roleId)
    {
        return $this->respond($this->userRoleService->getUserRoleAgents($roleId));
    }

    public function updateUserRolePermissions(UpdateUserRolePermissionsRequest $request)
    {
        $data = $request->validated();
        try {
            $role = $this->userRoleService->updateUserRolePermissions($data);
            return $this->respond($role, HttpStatuses::HTTP_OK, "User Role successfully updated!");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "User Role not found!");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function grantRevokeAgentFromRole(GrantRevokeAgentRoleRequest $request)
    {
        try {
            $data = $this->userRoleService->grantRevokeAgentFromRole($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "User Role successfully updated!");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }
}
