<?php

namespace App\Http\Requests\Api\TicketDocument;

use App\Http\Requests\Api\BaseFormRequest;

class UpdateTicketDocumentRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'documentUploadTypeId' => 'required',
        ];
    }
}
