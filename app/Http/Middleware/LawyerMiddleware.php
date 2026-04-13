<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'lawyer') {
            return redirect()->route('login')->withErrors(['email' => 'Access denied. Lawyer account required.']);
        }

        $profile = Auth::user()->lawyerProfile;
        if ($profile && $profile->availability_status === 'offline') {
            $profile->update(['availability_status' => 'available']);
        }

        return $next($request);
    }
}
