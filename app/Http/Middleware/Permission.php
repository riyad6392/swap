<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @return mixed
     */

    public function handle(Request $request, Closure $next, $role)
    {
        if (Auth::check()) {
            return $next($request);
        }

        return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
    }
}
