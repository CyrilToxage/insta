<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => 'nullable|string|max:2200',
            'image' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Génère un nom unique pour le fichier
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();

            // Stocke l'image
            $path = $request->file('image')->storeAs('posts', $fileName, 'public');
            $data['image'] = $path;
        }

        auth()->user()->posts()->create($data);

        return redirect()->route('profile.show', auth()->user())
            ->with('status', 'Post créé avec succès!');
    }

    public function destroy(Post $post)
    {
        // Vérifie si l'utilisateur est autorisé à supprimer ce post
        $this->authorize('delete', $post);

        // Supprime l'image du post
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->with('status', 'Post supprimé avec succès!');
    }
}
