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
    public function show(User $user): View
    {
        $posts = $user->posts()->latest()->paginate(12);
        return view('profile.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
{
    $user = $request->user();
    $data = $request->validated();

    if ($request->hasFile('profile_photo')) {
        // Supprimer l'ancienne photo si elle existe
        if ($user->profile_photo) {
            $oldPath = public_path($user->profile_photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Sauvegarder la nouvelle photo
        $file = $request->file('profile_photo');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Créer le dossier si nécessaire
        $path = public_path('images/profile');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Déplacer le fichier
        $file->move($path, $filename);
        $data['profile_photo'] = 'images/profile/' . $filename;
    }

    // Mise à jour de l'email
    if ($user->email !== $data['email']) {
        $user->email_verified_at = null;
    }

    // Mise à jour des données
    $user->fill($data);
    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function follow(User $user)
    {
        // Ne peut pas se suivre soi-même
        if (auth()->id() === $user->id) {
            return back();
        }

        // Toggle le follow (follow si pas encore follow, unfollow si déjà follow)
        auth()->user()->following()->toggle($user->id);

        return back();
    }

    public function unfollow(User $user): RedirectResponse
    {
        Auth::user()->following()->detach($user->id);
        return back();
    }
}
