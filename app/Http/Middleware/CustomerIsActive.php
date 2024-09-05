<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->account_active) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is not active',
                'data'    => null
            ], 400);
        }

        return $next($request);
    }
}
