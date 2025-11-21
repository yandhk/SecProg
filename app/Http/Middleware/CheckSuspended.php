<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuspended
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user sudah login
        if (Auth::check()) {

            // Cek apakah user suspended
            if (Auth::user()->is_suspended) {

                // Logout user
                Auth::logout();

                // Redirect ke login dengan pesan
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been suspended. Please contact admin.'
                ]);
            }
        }

        return $next($request);
    }
}
