<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();
        foreach($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return response()->json([
            'error' => 'Forbidden, you are not authorized to do this action',
            'role' => $user->getRoleNames()->toArray()
        ], 403);
    }
}
