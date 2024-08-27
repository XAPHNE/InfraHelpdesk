<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user needs to change the password
        if (auth()->check() && auth()->user()->must_change_passwd && !$request->is('profile*')) {
            return redirect()->route('profile.index')->with('warning', 'You must change your password before proceeding.');
        }
        
        return $next($request);
    }
}
