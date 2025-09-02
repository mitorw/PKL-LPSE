<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule; // <-- Ini tambahan baru

class ProfileController extends Controller
{

    public function updateBasic(Request $request): RedirectResponse
    {
        try {
            $user = $request->user(); // Dapatkan user untuk validasi

            // ✅ Perbaikan: Tambahkan aturan unique dengan mengabaikan ID user saat ini
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:4'],
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    Rule::unique('users')->ignore($user->id),
                ],
            ], [
                'name.required' => 'Nama wajib diisi.',
                'name.min' => 'Nama minimal 4 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid, gunakan format seperti nama@gmail.com.',
                'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.', // Pesan kesalahan kustom
            ]);

            // ✅ Update data user
            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return Redirect::route('profile.edit')
                ->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $pageTitle = 'Profil';

        return view('profile.edit', [
            'user' => $request->user(),
            'pageTitle' => $pageTitle,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user(); // Dapatkan user untuk validasi

        // ✅ Perbaikan: Tambahkan aturan unique dengan mengabaikan ID user saat ini
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:4'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 4 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid, gunakan format seperti nama@gmail.com.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.', // Pesan kesalahan kustom
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // karena dikirim base64 string
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->profile_photo && \Storage::exists('public/' . $user->profile_photo)) {
            \Storage::delete('public/' . $user->profile_photo);
        }

        // Ambil data base64
        $image = $request->input('image');
        $image = preg_replace('#^data:image/\w+;base64,#i', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageName = 'profile_' . uniqid() . '.png';

        // Simpan ke storage
        \Storage::disk('public')->put('profile_photos/' . $imageName, base64_decode($image));

        // Update database
        $user->profile_photo = 'profile_photos/' . $imageName;
        $user->save();

        return response()->json(['success' => true, 'path' => $user->profile_photo]);
    }
}