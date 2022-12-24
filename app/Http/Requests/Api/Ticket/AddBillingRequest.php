<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class AddBillingRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => '',
            'transactionNumber' => '',
            'specialNotes' => '',
            'invoice' => 'nullable|file',
            'doNotCharge' => ''
        ];
    }
}
