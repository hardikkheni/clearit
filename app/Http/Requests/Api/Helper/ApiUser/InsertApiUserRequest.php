<?php

namespace App\Http\Requests\Api\Helper\ApiUser;

use App\Http\Requests\Api\BaseFormRequest;

class InsertApiUserRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|max:255',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'email|required|max:255',
            'company' => 'required|max:255',
            'token' => '',
            'isActive' => 'boolean',
        ];
    }
}
