<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawFirmMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'law_firm') {
            return redirect()->route('login')->withErrors(['email' => 'Access denied. Law firm account required.']);
        }
        return $next($request);
    }
}
