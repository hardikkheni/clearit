<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class AddPgaRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pgaId' => 'required',
            'note' => '',
        ];
    }
}
