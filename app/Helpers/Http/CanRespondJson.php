<?php

namespace App\Helpers\Http;

trait CanRespondJson
{
    public function respond($data = [], $status = HttpStatuses::HTTP_OK, $message = '')
    {
        return response()->json(['data' => $data, 'message' => $message, 'status_code' => $status], $status);
    }
}
