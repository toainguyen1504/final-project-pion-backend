<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Gửi lại email xác thực
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email đã xác thực rồi.'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Đã gửi lại email xác thực.'
        ]);
    }

    /**
     * Xác thực email từ link (REAL - không cần login)
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Check hash
        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            return response()->json([
                'message' => 'Link xác thực không hợp lệ.'
            ], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            // update status nếu cần
            if ($user->status === User::STATUS_UNVERIFIED) {
                $user->update([
                    'status' => User::STATUS_ACTIVE,
                ]);
            }
        }

        // redirect về FE
        return redirect()->away(config('app.frontend_url') . '/xac-thuc-email?success=1');
    }
}
