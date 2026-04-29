<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    // update profile
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
        ]);

        $newEmail = $validated['email'] ?? null;
        $oldEmail = $user->email;

        $emailChanged = $newEmail !== $oldEmail;

        $user->display_name = $validated['display_name'];
        $user->phone = $validated['phone'] ?? null;
        $user->email = $newEmail;

        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->status = User::STATUS_UNVERIFIED;
        }

        $user->save();

        if ($emailChanged && $user->email) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'message' => $emailChanged
                ? 'Cập nhật hồ sơ thành công. Vui lòng xác thực lại email mới.'
                : 'Cập nhật hồ sơ thành công.',
            'user' => $user,
        ]);
    }


    // change password
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu cũ không chính xác.',
            ], 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }
}
