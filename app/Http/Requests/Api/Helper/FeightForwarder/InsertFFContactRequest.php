<?php

namespace App\Http\Requests\Api\Helper\FeightForwarder;

use App\Http\Requests\Api\BaseFormRequest;

class InsertFFContactRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'isfcName' => 'required|max:255',
            'isfcBusinessPhone' => '',
            'isfcMobilePhone' => '',
            'isfcEmailAddress' => 'email|required|max:255',
            'isDefault' => 'boolean',
        ];
    }
}
