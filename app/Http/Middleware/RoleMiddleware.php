<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();
        $userRole = $user->role;

        // Cek apakah user punya role
        if (!$userRole) {
            abort(403, 'Akun Anda tidak memiliki role. Silakan hubungi administrator.');
        }

        $userRoleName = $userRole->nama_role;

        // Cek apakah role user diizinkan (case insensitive)
        $allowed = false;
        foreach ($roles as $role) {
            if (strtolower(trim($userRoleName)) === strtolower(trim($role))) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            // Redirect ke dashboard sesuai role yang sebenarnya
            $redirectPath = match (strtolower($userRoleName)) {
                'owner' => route('owner.dashboard'),
                'kasir' => route('kasir.dashboard'),
                'gudang' => route('gudang.dashboard'),
                default => '/login'
            };

            return redirect($redirectPath)->with('error', 'Anda tidak memiliki akses ke halaman Owner. Anda login sebagai ' . $userRoleName);
        }

        return $next($request);
    }
}