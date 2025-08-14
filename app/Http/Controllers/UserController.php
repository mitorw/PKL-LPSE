<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/');
        }

        $users = User::all();
        return view('manajemen_akun.index', compact('users'));
    }

    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }


        return view('manajemen_akun.create');
    }
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }


        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('manajemen_akun.index')->with('success', 'Akun berhasil ditambahkan');
    }

    /**
     * Mengubah role dari user.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Proteksi manual: Hanya admin yang bisa mengakses.
        if (Auth::user()->role !== 'admin') {
            return redirect('/');
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('manajemen_akun.index')->with('success', 'Role user berhasil diubah.');
    }
}
