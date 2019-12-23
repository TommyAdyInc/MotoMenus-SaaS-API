<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdmin
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        if (request()->wantsJson()) {
            return response('Unauthorized.', 401);
        }

        return redirect('/');
    }
}
