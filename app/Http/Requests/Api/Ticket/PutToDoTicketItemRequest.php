<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class PutToDoTicketItemRequest extends BaseFormRequest
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
            'status' => 'required|integer',
            'toDoItemDependencyIds' => 'array'
        ];
    }
}
