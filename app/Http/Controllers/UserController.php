<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('owner.pengguna', compact('users', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:3',
            'role_id' => 'required|exists:roles,role_id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user->load('role')
            ]);
        }

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $user
                ]);
            }

            return view('owner.pengguna-edit', compact('user'));

        } catch (ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            return redirect()->route('owner.pengguna.index')
                ->with('error', 'User tidak ditemukan');
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:100',
                'username' => 'required|string|unique:users,username,' . $id . ',user_id',
                'role_id' => 'required|exists:roles,role_id',
                'password' => 'nullable|min:3'
            ]);

            $user->name = $request->name;
            $user->username = $request->username;
            $user->role_id = $request->role_id;

            // Only update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil diupdate',
                    'data' => $user->load('role')
                ]);
            }

            return redirect()->back()->with('success', 'User berhasil diupdate');

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting own account
            if ($user->user_id == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri'
                ], 403);
            }

            // Optional: Prevent deleting specific protected accounts
            $protectedUsernames = ['owner'];
            if (in_array($user->username, $protectedUsernames)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun protected'
                ], 403);
            }

            $userName = $user->name;
            $user->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User '{$userName}' berhasil dihapus"
                ]);
            }

            return redirect()->back()->with('success', "User '{$userName}' berhasil dihapus");

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $user
                ]);
            }

            return view('owner.pengguna-show', compact('user'));

        } catch (ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            return redirect()->route('owner.pengguna.index')
                ->with('error', 'User tidak ditemukan');
        }
    }

    /**
     * Get users data for datatable (if needed)
     */
    public function getData(Request $request)
    {
        $users = User::with('role')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Bulk delete users (optional feature)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,user_id'
        ]);

        try {
            // Prevent deleting own account and protected accounts
            $ids = array_filter($request->ids, function ($id) {
                $user = User::find($id);
                if (!$user)
                    return false;

                // Don't delete own account
                if ($user->user_id == auth()->id())
                    return false;

                // Don't delete protected accounts
                $protectedUsernames = ['admin', 'owner'];
                if (in_array($user->username, $protectedUsernames))
                    return false;

                return true;
            });

            $deletedCount = User::whereIn('user_id', $ids)->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$deletedCount} user berhasil dihapus"
                ]);
            }

            return redirect()->back()->with('success', "{$deletedCount} user berhasil dihapus");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
}