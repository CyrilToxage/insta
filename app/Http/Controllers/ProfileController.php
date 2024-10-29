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
            $data['profile_photo'] = $request->file('profile_photo')
                                    ->store('profile-photos', 'public');
        }

        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

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

    public function follow(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back();
        }

        Auth::user()->following()->attach($user->id);
        return back();
    }

    public function unfollow(User $user): RedirectResponse
    {
        Auth::user()->following()->detach($user->id);
        return back();
    }
}
