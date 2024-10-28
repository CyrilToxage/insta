<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            // Supprime l'ancienne photo si elle existe
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Stocke la nouvelle photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        if ($request->hasFile('profile_photo')) {
            // Traitement de l'image
            $image = $request->file('profile_photo');

            // Génère un nom unique pour le fichier
            $fileName = time() . '_' . $image->getClientOriginalName();

            // Stocke l'image
            $path = $image->storeAs('profile-photos', $fileName, 'public');
            $data['profile_photo'] = $path;
        }

        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($data);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Pour supprimer un compte et ses fichiers associés
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        // Supprime la photo de profil
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Supprime toutes les photos des posts
        foreach ($user->posts as $post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->delete();
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
