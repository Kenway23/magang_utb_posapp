<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:3',
            'role_id' => 'required|exists:roles,role_id'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role_id' => $request->role_id
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }
}