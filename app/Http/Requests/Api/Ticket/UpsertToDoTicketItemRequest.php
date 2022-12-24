<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class UpsertToDoTicketItemRequest extends BaseFormRequest
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
            'role' => 'required|integer',
            'id' => '',
            'itemName' => 'required|max:255',
            'displayOrder' => '',
        ];
    }
}
