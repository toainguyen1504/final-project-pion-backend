<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/admin')->with('success', 'Đăng nhập hệ thống quản trị thành công!');
        }

        return back()->with('error', 'Email hoặc mật khẩu không đúng.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
