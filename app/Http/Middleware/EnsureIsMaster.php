<?php

namespace App\Http\Middleware;

use App\Helpers\Http\CanRespondJson;
use App\Helpers\Http\HttpStatuses;
use Closure;
use Illuminate\Http\Request;

class EnsureIsMaster
{

    use CanRespondJson;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->isMaster) {
            return $this->respond([], HttpStatuses::HTTP_FORBIDDEN, 'You don\'t have permission for this action!.');
        }
        return $next($request);
    }
}
