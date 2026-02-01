<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private function attemptLogin(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
            return null;
        }

        return Auth::user();
    }

    // Login cho CMS
    public function loginCms(Request $request)
    {
        $user = $this->attemptLogin($request);


        if (!$user) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ!'], 401);
        }

        // Chỉ cho phép role: staff, admin, super_admin, teacher
        if (!in_array($user->role->name, ['staff', 'admin', 'super_admin', 'teacher'])) {
            return response()->json(['message' => 'Bạn không có quyền đăng nhập CMS!'], 403);
        }

        $token = $user->createToken('cms-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
            'role'  => $user->role->name,
        ]);
    }

    // Login cho Client site
    public function loginClient(Request $request)
    {
        $user = $this->attemptLogin($request);

        if (!$user) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ!'], 401);
        }

        // Chỉ cho phép role: member, guest, learner
        if (!in_array($user->role->name, ['member', 'guest', 'learner'])) {
            return response()->json(['message' => 'Bạn không có quyền đăng nhập Client site!'], 403);
        }

        $token = $user->createToken('client-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
            'role'  => $user->role->name,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công.'
        ]);
    }
}
