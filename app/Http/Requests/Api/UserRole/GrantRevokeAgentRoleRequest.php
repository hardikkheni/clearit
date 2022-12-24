<?php

namespace App\Http\Requests\Api\UserRole;

use App\Http\Requests\Api\BaseFormRequest;

class GrantRevokeAgentRoleRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'roleId' => 'required|integer',
            'agentId' => 'required|integer'
        ];
    }
}
