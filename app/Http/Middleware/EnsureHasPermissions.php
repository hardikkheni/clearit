<?php

namespace App\Http\Middleware;

use App\Helpers\Http\CanRespondJson;
use App\Helpers\Http\HttpStatuses;
use Closure;
use Illuminate\Http\Request;

class EnsureHasPermissions
{

    use CanRespondJson;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $permissions = config('constants.permission');
        $roleBitMask = (int) @$permissions[$permission];
        if (((int) $request->user()->permissionBitmask & $roleBitMask)  != $roleBitMask) {
            return $this->respond([], HttpStatuses::HTTP_FORBIDDEN, 'You don\'t have permission for this action!.');
        }
        return $next($request);
    }
}
