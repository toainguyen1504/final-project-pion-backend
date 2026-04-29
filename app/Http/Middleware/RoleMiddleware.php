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
            return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập.'], 401);
        }
        
        $roleList = explode('|', $roles);
        if (!$user->role || !in_array($user->role->name, $roleList)) {
            return response()->json(['success' => false, 'message' => 'Truy cập bị từ chối. Bạn không có quyền thực hiện thao tác này.'], 403);
        }
        return $next($request);
    }
}
