<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class AuthController extends BaseApiController
{

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        try {
            $loginInfo = $this->authService->login($data, $request->ip());
            return $this->respond($loginInfo);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "User not found!");
        } catch (UnauthorizedException $e) {
            return $this->respond(null, HttpStatuses::HTTP_UNAUTHORIZED, $e->getMessage());
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function profile(Request $request)
    {
        return $this->respond(['user' => $request->user(),]);
    }

    public function dashboard(Request $request)
    {
        $data = $request->all();
        return $this->respond($this->authService->dashboard($data));
    }
}
