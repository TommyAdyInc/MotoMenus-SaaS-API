<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 10/28/2019
 * Time: 2:38 PM
 */

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;

class AuthOrSuperAdmin
{
    public function handle($request, Closure $next)
    {
        try {
            if (Auth::guard('api')->check() || Auth::guard('api_super_admin')->check()) {
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
