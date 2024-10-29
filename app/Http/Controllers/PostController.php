<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $following_ids = auth()->user()->following()->pluck('users.id');
        $following_ids->push(auth()->id());

        $posts = Post::whereIn('user_id', $following_ids)
            ->with(['user', 'likes', 'comments'])
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'caption' => 'nullable|string|max:2200',
            'image' => 'required|image|max:2048'
        ]);

        // Récupération du fichier
        $file = $request->file('image');

        // Création d'un nom unique pour l'image
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        // Déplacement du fichier vers le dossier public
        $file->move(public_path('images/posts'), $filename);

        // Création du post dans la base de données
        $post = Post::create([
            // ID de l'utilisateur qui crée le post
            'user_id' => auth()->id(),

            // Chemin de l'image (sera utilisé pour afficher l'image plus tard)
            'image' => 'images/posts/' . $filename,

            // Texte de la légende (peut être null si pas de légende)
            'caption' => $request->caption
        ]);

        // Redirection vers le profil de l'utilisateur
        return redirect()
            ->route('profile.show', auth()->user())  // Route avec l'utilisateur comme paramètre
            ->with('status', 'Post créé avec succès!');  // Message de succès
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
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

    public function like(Post $post)
    {
        auth()->user()->likes()->toggle($post->id);
        return back();
    }
}
