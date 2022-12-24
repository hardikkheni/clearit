<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class BaseFormRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new JsonResponse([
            'data' => [],
            'message' => 'The given data is invalid',
            'errors' => $validator->errors(),
            'status_code' => 422
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
