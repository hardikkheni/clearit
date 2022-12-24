<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\CanRespondJson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    use CanRespondJson;
}
