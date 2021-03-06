<?php


namespace App\Http\Middleware;


use Closure;

class ProviderDetector
{
    public function handle($request, Closure $next)
    {
        $validator = validator()->make($request->all(), [
            'username' => 'required',
            'provider' => 'required|in:superadmins,users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag(),
                'status_code' => 422
            ], 422);
        }

        config(['auth.guards.api.provider' => $request->input('provider')]);

        return $next($request);
    }
}
