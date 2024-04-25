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
            $userSubscriptions = $user->subscriptions()->where('status', 'active')->get();

            if ($userSubscriptions->isEmpty()) {
                $user->subscription_is_active = 0;
                return response()->json([
                    'success' => false,
                    'message' => 'You are not subscribed to any plan.'
                ], 403);
            }
            elseif ( $userSubscriptions->isNotEmpty() && $userSubscriptions->last()->end_date < now()) {
                $user->subscription_is_active = 0;
                return response()->json([
                    'success' => false,
                    'message' => 'Your subscription has expired.'
                ], 403);
            }
            else {
                $user->subscription_is_active = 1;
                return $next($request);
            }
        }

        return $next($request);
    }
}
