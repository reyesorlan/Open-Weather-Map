<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAPITokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-API-KEY') !== env('API_KEY')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API key',
            ], 401);
        }
        return $next($request);
    }
}
