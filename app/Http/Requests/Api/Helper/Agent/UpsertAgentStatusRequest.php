<?php

namespace App\Http\Requests\Api\Helper\Agent;

use App\Http\Requests\Api\BaseFormRequest;

class UpsertAgentStatusRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|max:255',
            'role' => 'required|integer',
            'id' => '',
            'statusName' => 'required|max:255',
            'displayOrder' => '',
        ];
    }
}
