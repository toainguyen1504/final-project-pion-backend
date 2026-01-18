<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
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
            'password'     => 'nullable|string|min:8',       // không bắt buộc
        ]);

        try {
            // Nếu không nhập password -> tự sinh
            if (!$request->filled('password')) {
                $passwords = \App\Services\PasswordService::generate(10);
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

            return response()->json([
                'success'        => true,
                'message'        => 'User created successfully!',
                'data'           => $user,
                'plain_password' => $plainPassword // trả về cho admin biết
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user.'
            ], 500);
        }
    }


    public function show($id)
    {
        $user = User::with(['role', 'learner', 'teacher', 'staff'])->find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
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
                'message' => 'User updated successfully!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.'
            ], 500);
        }
    }
}
