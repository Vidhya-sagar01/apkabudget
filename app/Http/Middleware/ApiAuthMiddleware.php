<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = strtolower($request->header('User-Agent', ''));
        if (str_contains($userAgent, 'mozilla') && str_contains($userAgent, 'mobile')) {
            return response()->json([
                //'status' => false,
                'message' => ''
            ], 401);
        }
        // Log::channel('api_request')->info([
        //     'method' => $request->method(),
        //     'url' => $request->fullUrl(),
        //     'ip' => $request->ip(),
        //     'user_agent' => $request->header('User-Agent'),
        // ]);

        $user = Auth::guard('sanctum')->user();
        if (!$user && session('api_token')) {
            $token = session('api_token');
            $user = \App\Models\User::where('id', session('user_id'))->first();

            if ($user) {
                // Optional: validate token matches stored token if saved in DB
                $request->setUserResolver(fn() => $user);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'qwUnauthorized access. Invalid token.'
                ], 401);
            }
        }
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. Please provide a valid API token.'
            ], 401);
        }
        
        // $today = Carbon::today();
        // if ($user->created_at >= $today) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Access denied. Recently registered users are not allowed.'
        //     ], 403);
        // }

        // Set the authenticated user for further use in the request
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
