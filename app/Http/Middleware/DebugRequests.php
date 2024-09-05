<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugRequests
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
      if(!config('settings.requests-logs')) return $next($request);

      $response = $next($request);

      $time = microtime(true) - LARAVEL_START;

      switch (true) {
        case $time < 0.5:
          $status = "is-ok";
          return $response;
          break;
        case $time < 1.01:
          $status = "can-be-improve";
          break;
        default:
          $status = "need-to-check";
          break;
      }

      $message = "Status: ".$status." | Time: ".$time. " | URL: ".$request->server('HTTP_HOST').$request->server('REQUEST_URI')."| ".$request->server('REQUEST_METHOD')." | Agent:".$request->header('user-agent');
      return $response;
    }
}
