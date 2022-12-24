<?php

namespace App\Http\Requests\Api\Helper\Notification;

use App\Http\Requests\Api\BaseFormRequest;

class MarkViewedNotificationRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids' => 'array',
            'all' => 'boolean',
            'is_affiliate' => ''
        ];
    }
}
