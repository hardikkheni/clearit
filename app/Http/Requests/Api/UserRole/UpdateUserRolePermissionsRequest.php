<?php

namespace App\Http\Requests\Api\UserRole;

use App\Http\Requests\Api\BaseFormRequest;

class UpdateUserRolePermissionsRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role' => 'required|max:255',
            'permissions' => ''
        ];
    }
}
