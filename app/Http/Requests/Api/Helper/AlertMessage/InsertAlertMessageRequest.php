<?php

namespace App\Http\Requests\Api\Helper\AlertMessage;

use App\Http\Requests\Api\BaseFormRequest;

class InsertAlertMessageRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => 'required|max:255',
            'messageBody' => '',
            'acknowledgementRequired' => 'boolean',
            'showNewAgent' => 'boolean',
            'isActive' => 'boolean',
        ];
    }
}
