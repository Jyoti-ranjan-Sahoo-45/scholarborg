<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EdgeCachePrimer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
