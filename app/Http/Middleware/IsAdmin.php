<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 10/28/2019
 * Time: 2:38 PM
 */

namespace App\Http\Middleware;


use Closure;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        try {
            if ($request->user()->user_role->role === 'admin') {
                return $next($request);
            }

            if (request()->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect('/');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect('/');
        }
    }
}