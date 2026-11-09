<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin has access to all routes if needed, but let's check roles
        if (! in_array($user->role, $roles, true) && $user->role !== 'admin') {
            abort(403, 'Akses tidak diizinkan untuk peran akun Anda (' . ucfirst($user->role) . ').');
        }

        return $next($request);
    }
}
