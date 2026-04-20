<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PasswordService;
use App\Models\User;
use App\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    // Thống kê đơn giản
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
            ],
        ]);
    }

    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'updated_at');
        $order = request()->get('order', 'desc');
        $search = request()->get('search');

        $query = User::with('role');

        if ($search) {
            $query->where('display_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy($sort, $order)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl()
            ]
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'role_id'      => 'required|exists:roles,id',
            'status'       => 'nullable|integer|in:0,1,2',
            'email'        => 'nullable|email|unique:users', // không bắt buộc
            'password'     => 'nullable|string|min:6',       // không bắt buộc
        ]);

        try {
            // Nếu không nhập password -> tự sinh 6 ký tự
            if (!$request->filled('password')) {
                $passwords = \App\Services\PasswordService::generate(6);
                $validated['password'] = $passwords['hashed'];
                $plainPassword = $passwords['plain'];
            } else {
                $validated['password'] = Hash::make($request->password);
                $plainPassword = $request->password;
            }

            // Nếu không truyền status thì mặc định là UNVERIFIED
            $validated['status'] = $validated['status'] ?? User::STATUS_UNVERIFIED;

            // Tạo user
            $user = User::create($validated);
            $user = $user->fresh(['role', 'learner', 'teacher', 'staff']);

            // Sinh username theo logic mới
            $username = \App\Services\UsernameService::generate($user);
            $user->update(['username' => $username]);

            return response()->json([
                'success'        => true,
                'message'        => 'Tạo tài khoản thành công!',
                'data'           => $user,
                'plain_password' => $plainPassword // trả về cho admin biết
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo tài khoản thất bại.'
            ], 500);
        }
    }


    public function show($id)
    {
        $user = User::with(['role', 'learner', 'teacher', 'staff'])->find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }


    // Update
    public function update(Request $request, $id)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        $user = User::find($id); // id
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản.'
            ], 404);
        }

        // Nếu chưa load role, load để đảm bảo có dữ liệu
        $user->loadMissing('role');
        if ($currentUser) {
            $currentUser->loadMissing('role');
        }

        // get role to check - chặn sửa super_admin
        if ($currentUser && $currentUser->hasRole('admin') && $user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin không có quyền sửa Super Admin.'
            ], 403);
        }

        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|unique:users,email,' . $id,
            'password'     => 'nullable|string|min:8',
            'role_id'      => 'sometimes|exists:roles,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        try {
            $user->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tài khoản thành công!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật tài khoản thất bại.'
            ], 500);
        }
    }

    // Delete
    public function destroy($id)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản.'
            ], 404);
        }

        $user->loadMissing('role');
        if ($currentUser) {
            $currentUser->loadMissing('role');
        }

        // chặn super_admin bị xóa
        if ($currentUser && $currentUser->hasRole('admin') && $user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin không có quyền xóa Super Admin.'
            ], 403);
        }

        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xóa tài khoản thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa tài khoản thất bại.'
            ], 500);
        }
    }

    // note: Email/Phone: cho phép chỉnh sửa khi tài khoản mới chưa có thông tin.
    //       Username: không cho phép user tự đổi, chỉ admin/super_admin mới có quyền.
    // Get profile staff/learner/teacher
    public function me(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user()->load(['role', 'learner', 'teacher', 'staff']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    // update profile staff/learner/teacher
    public function updateMe(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'profile_image' => 'sometimes|url',
            'password'     => 'nullable|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        try {
            $user->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin cá nhân thành công!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thông tin cá nhân thất bại.'
            ], 500);
        }
    }

    // Reset password - accept admin and super_admin
    public function resetPassword($id)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản.'
            ], 404);
        }

        // Admin không được reset password của Super Admin
        // TH này không thể xảy ra vì đã chặn 2 role ko được gán: guest và super_admin
        if ($currentUser->hasRole('admin') && $user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin không có quyền đặt lại mật khẩu của Super Admin.'
            ], 403);
        }

        try {
            // Sinh password mới
            $passwords = PasswordService::generate(10);
            $user->update(['password' => $passwords['hashed']]);

            return response()->json([
                'success'        => true,
                'message'        => 'Đặt lại mật khẩu thành công!',
                'username'       => $user->username,
                'plain_password' => $passwords['plain'] // trả về cho admin biết
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đặt lại mật khẩu thất bại.'
            ], 500);
        }
    }

    public function getRoles(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = $request->user();
        $currentUser->loadMissing('role');

        // Chỉ cho phép staff, admin, super_admin gọi API này
        if (!$currentUser->hasAnyRole(['staff', 'admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Truy cập bị từ chối. Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    // Lấy tất cả roles trừ super_admin và guest
    public function rolesAvailable(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = $request->user();
        $currentUser->loadMissing('role');

        // Chỉ cho phép admin, super_admin gọi API này
        if (!$currentUser->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Truy cập bị từ chối. Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $roles = Role::whereNotIn('name', ['super_admin', 'guest'])->get();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }
}
