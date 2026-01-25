<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Closure;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // dd($request);
        if ($request->expectsJson()) {
            abort(response()->json([
                'status'  => false,
                'message' => 'Unauthorized access. Please login first.'
            ], 401));
        }

        if ($request->is('admin/*')) {
            return route('admin.login');
        } 
        else if ($request->is('provider/*') || $request->is('user/*')) {
            // dd($request);
            return route('login.page');
        }
    }
}
