<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Kalau user adalah admin → redirect ke /courses
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect('/courses');
        }

        return $next($request);
    }
}
