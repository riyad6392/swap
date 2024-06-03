<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnverifiedSuperSwapper
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();

            if ($user->is_super_swapper == 0 && $user->is_approved_by_admin == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have no permission to access this route.'
                ], 403);
            }
        }
        return $next($request);
    }
}
