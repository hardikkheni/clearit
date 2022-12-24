<?php

namespace App\Http\Requests\Api\Helper\ClientRequest;

use App\Http\Requests\Api\BaseFormRequest;

class BulkInsertClientReqRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ticketId' => 'required',
            'roleId' => '',
            'client_guid' => 'required',
            'client_requests' => 'required|array|min:1',
            'client_requests.*.documentTypeId' => '',
            'client_requests.*.sampleDocumentURL' => '',
            'client_requests.*.description' => '',
        ];
    }
}
