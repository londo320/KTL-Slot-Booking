<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
{
    public function handle($request, Closure $next)
    {
        if (! $request->user()?->is_admin) {
            abort(403);
        }
        return $next($request);
    }
}