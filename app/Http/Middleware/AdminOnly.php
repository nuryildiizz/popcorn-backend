<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // is_admin kolonu (en net yol)
        if ((bool) $user->getAttribute('is_admin')) {
            return $next($request);
        }

        // roles ilişkisi varsa
        if (method_exists($user, 'roles') && $user->roles()->where('name', 'admin')->exists()) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
