<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()) {
            $expireAt = now()->addMinutes(1);
            Cache::store('redis')->put('active_users_' . auth()->id(), true, $expireAt);
            auth()->user()->update([
                'active_at' => Carbon::now()->toISOString(),
            ]);
        }
        return $next($request);
    }
}
