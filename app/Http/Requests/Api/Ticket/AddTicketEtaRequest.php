<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class AddTicketEtaRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'containerNumber' => '',
            'etaComment' => '',
            'eta' => 'nullable|date',
            'disableEtaEmails' => 'boolean',
            'lastFreeDay' => 'nullable|date',
            'mBOL' => '',
            'hBOL' => '',
        ];
    }
}
