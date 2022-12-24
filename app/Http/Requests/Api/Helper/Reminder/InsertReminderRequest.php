<?php

namespace App\Http\Requests\Api\Helper\Reminder;

use App\Http\Requests\Api\BaseFormRequest;

class InsertReminderRequest extends BaseFormRequest
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
            'assignedToUserId' => 'required',
            'dueOnDate' => 'required',
            'dueOnTime' => '',
            'message' => 'required',
        ];
    }
}
