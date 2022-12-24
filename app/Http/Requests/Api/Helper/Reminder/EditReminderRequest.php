<?php

namespace App\Http\Requests\Api\Helper\Reminder;

use App\Http\Requests\Api\BaseFormRequest;

class EditReminderRequest extends BaseFormRequest
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
            'assignedToAgentId' => 'required',
            'dueOnDate' => 'required',
            'dueOnTime' => 'required',
            'message' => 'required',
        ];
    }
}
