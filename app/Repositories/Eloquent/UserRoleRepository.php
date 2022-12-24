<?php

namespace App\Repositories\Eloquent;

use App\Models\UserRole;

class UserRoleRepository extends BaseRepository
{

    const MODEL_LABEL = 'User Role';

    const GetTicketRoleStatusFunction = 'getTicketRoleStatus';

    public function __construct(UserRole $model)
    {
        parent::__construct($model);
    }

    public function active()
    {
        return $this->model->active()->get();
    }

    /**
     * @param array|null $user
     * 
     * @return object
     */
    public function dashboardRoles($roleBitmask = null)
    {
        $query = $this->model->active()->dashboard();
        if ($roleBitmask != null) {
            $query->whereRaw("(bitmaskValue & \"$roleBitmask\") > 0");
        }
        return $query->orderBy('displayOrder', 'ASC')->get();
    }

    /**
     * Procedure for getting all Agents by roles
     * @param mixed  $roleId
     * 
     * 
     * @return array
     */
    public function getRoleAgents($id)
    {
        $data = $this->call('getRoleAgents', [$id]);
        return $data[0] ?? [];
    }

    public function saveAgentRole($userId, $roleId)
    {
        $data = $this->call('saveAgentRole', [$userId, $roleId]);
        return $data[0] ?? [];
    }

    public function findOneByInteralKey($internalKey)
    {
        return $this->findOne([['internalKey', $internalKey]]);
    }
}
