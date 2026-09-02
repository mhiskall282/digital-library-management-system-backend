<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->canModerate()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Administrator or Staff access required.'], 403);
            }

            abort(403, 'Access restricted to School of Business administrators and library staff.');
        }

        return $next($request);
    }
}
