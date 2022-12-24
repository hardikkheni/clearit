<?php

namespace App\Http\Requests\Api\TicketUserHts;

use App\Http\Requests\Api\BaseFormRequest;

class UpdateTicketUserHtsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guid' => 'required',
            'ticketId' => 'required',
            'uhtsId' => 'required',
            'code' => 'required',
            'description' => '',
            'sku' => '',
            'role' => '',
        ];
    }
}
