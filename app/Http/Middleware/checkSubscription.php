<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
            if ($user->subscription_is_check) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not subscribed to any plan.'
                ], 403);
            }
        }
        return $next($request);
    }
}
