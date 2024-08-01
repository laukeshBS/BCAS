<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResetLastActive
{
    public function handle($request, Closure $next)
    {
        Session::put('lastActive', date('U'));
      //  session(['lastActive' => date('U')]); // Set session value using session() helper function
        Session::forget('idleWarningDisplayed');
        Session::forget('logoutWarningDisplayed');

        return $next($request);
    }
}
