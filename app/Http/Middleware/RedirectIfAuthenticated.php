<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->is('api/*')) {

            abort(response()->json([
                
                'status_code' => 401,
                'error' => true,
                'message' => 'Unauthenticated'
                
            ]));

        } else {

           return url('/');
        }
    }
}
