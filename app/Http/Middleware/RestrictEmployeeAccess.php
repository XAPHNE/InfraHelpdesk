<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictEmployeeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user->isEmployee) {
            // Redirect employees away from the dashboard and user management pages
            if ($request->is('dashboard') || $request->is('user-management/*') || $request->is('user-management')) {
                return redirect()->route('ticket-management.index');
            }
        }

        if ($user->isVendor) {
            // Redirect vendors away from user management pages
            if ($request->is('user-management/*') || $request->is('user-management')) {
                return redirect()->route('dashboard');
            }
        }
        
        return $next($request);
    }
}
