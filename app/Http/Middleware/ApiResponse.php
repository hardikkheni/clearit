<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($response->headers->get('content-type') == 'application/json') {
            $collection = $response->original;
            $response->setContent(json_encode(['data' => $collection, 'status' => 100]));
        }

        return $response;
    }
}
