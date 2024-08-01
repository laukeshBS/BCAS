<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (session()->has('locale')) {
            app()->setLocale(session()->get('locale'));
        } else {
            app()->setLocale(config('app.locale'));
        }
    
        return $next($request);
    }
    
}

