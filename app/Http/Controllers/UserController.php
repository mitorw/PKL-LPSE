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
