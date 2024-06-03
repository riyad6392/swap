<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApproval
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

            if ($user->is_admin_approved == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not approved by admin.'
                ], 403);
            }
        }
        return $next($request);
    }
}
