<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = $user->role;

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak memiliki role.'
                ], 403);
            }

            $roleName = strtolower($role->nama_role);

            $redirect = match ($roleName) {
                'owner' => route('owner.dashboard'),
                'kasir' => route('kasir.dashboard'),
                'gudang' => route('gudang.dashboard'),
                default => null
            };

            if (!$redirect) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak dikenal'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => $redirect,
                'user' => $user->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}