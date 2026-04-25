<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('users.username', $request->username)
            ->select(
                'users.user_id as user_id',
                'users.username',
                'users.password',
                'roles.nama_role as nama_role'
            )
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Username tidak ditemukan'
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ]);
        }

        // simpan session
        session([
            'user_id' => $user->user_id,
            'username' => $user->username,
            'role' => $user->nama_role
        ]);

        // redirect berdasarkan role
        $redirect = match ($user->nama_role) {
            'owner' => route('owner.dashboard'),
            'gudang' => route('gudang.dashboard'),
            'kasir' => route('kasir.dashboard'),
            default => '/login'
        };

        return response()->json([
            'success' => true,
            'redirect' => $redirect
        ]);
    }
}