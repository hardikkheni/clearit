<?php

namespace App\Http\Requests\Api\Helper\Agent;

use App\Http\Requests\Api\BaseFormRequest;

class EditAgentRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => '',
            'login' => 'required|max:255',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'email|required|max:255',
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'city' => 'required|max:255',
            'isActive' => 'boolean',
            'isMaster' => 'boolean',
            'displayInternally' => 'boolean',
            'permissions' => ''
        ];
    }
}
