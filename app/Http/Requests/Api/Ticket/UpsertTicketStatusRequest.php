<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class UpsertTicketStatusRequest extends BaseFormRequest
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
            'transport' => 'required|integer',
            'id' => '',
            'statusName' => 'required|max:255',
            'substatus' => '',
            'hexColor' => 'required|max:255',
            'textHexColor' => 'required|max:255',
            'displayOrder' => '',
        ];
    }
}
