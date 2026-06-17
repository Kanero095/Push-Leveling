<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetUserLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->locale) {
            App::setLocale(Auth::user()->locale);
        } elseif (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}
