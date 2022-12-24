<?php

namespace App\Http\Requests\Api\Helper\FeightForwarder;

use App\Http\Requests\Api\BaseFormRequest;

class InsertFeightForwarderRequest extends BaseFormRequest
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
            'isfcName' => 'required|max:255',
            'isfcAddress1' => 'required|max:255',
            'isfcAddress2' => '',
            'isfcCountry' => 'required|max:255',
            'isfcState' => '',
            'isfcCity' => 'required|max:255',
            'isfcZip' => 'required|max:255',
            'isfcBusinessPhone' => 'required|max:255',
        ];
    }
}
