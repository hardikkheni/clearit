<?php

namespace App\Http\Requests\Api\Note;

use App\Http\Requests\Api\BaseFormRequest;

class CreateNoteRequest extends BaseFormRequest
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
            'notefile' => 'nullable|file',
            'description' => 'required'
        ];
    }
}
