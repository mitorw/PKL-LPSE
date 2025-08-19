<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        $pageTitle = 'Manajemen Akun';

        // Proteksi manual: Hanya admin yang bisa mengakses.
        if (Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $users = User::all();
        return view(
            'manajemen_akun.index', compact('users')
        )->with('pageTitle', $pageTitle);
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

    // --- FUNGSI BARU UNTUK RESET PASSWORD ---
    /**
     * Mereset password user ke password default.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        // Proteksi: Hanya admin yang bisa mengakses.
        if (Auth::user()->role !== 'admin') {
            abort(403, 'AKSES DITOLAK.');
        }

        // Set password default
        $defaultPassword = 'password@12345';
        $user->password = Hash::make($defaultPassword); // Gunakan Hash::make()
        $user->save();

        return redirect()->route('manajemen_akun.index')->with('success', 'Password untuk akun ' . $user->name . ' berhasil direset.');
    }

    // --- FUNGSI BARU UNTUK HAPUS AKUN ---
    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Proteksi: Hanya admin yang bisa mengakses.
        if (Auth::user()->role !== 'admin') {
            abort(403, 'AKSES DITOLAK.');
        }

        // Proteksi tambahan: Admin tidak bisa menghapus akunnya sendiri
        if (Auth::id() === $user->id) {
            return redirect()->route('manajemen_akun.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('manajemen_akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}
