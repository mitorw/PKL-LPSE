<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Mendefinisikan variabel $pageTitle
        $pageTitle = 'Profil';

        return view('profile.edit', [
            'user' => $request->user(),
            // Mengirimkan variabel $pageTitle ke view
            'pageTitle' => $pageTitle,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
