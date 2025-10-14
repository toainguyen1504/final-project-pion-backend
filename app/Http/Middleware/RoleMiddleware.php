<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $roleList = explode('|', $roles);

        if (!$user->role || !in_array($user->role->name, $roleList)) {
            return response()->json([
                'message' => 'Access denied. You do not have permission.'
            ], 403);
        }

        return $next($request);
    }
}
