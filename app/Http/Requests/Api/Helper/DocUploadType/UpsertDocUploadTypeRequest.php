<?php

namespace App\Http\Requests\Api\Helper\DocUploadType;

use App\Http\Requests\Api\BaseFormRequest;

class UpsertDocUploadTypeRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => '',
            'shipping_method' => '',
            'document_type' => 'required|max:255',
            'document_description' => '',
            'is_required' => '',
            'show_affiliate' => '',
            'show_customer' => '',
            'show_freight_forwarder' => '',
            'permissions' => '',
        ];
    }
}
