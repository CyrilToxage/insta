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
        // 1. Validation de base
        $data = $request->validate([
            'caption' => 'nullable|string|max:2200',
            'image' => 'required|image|max:2048'
        ]);

        // 2. Vérification de l'image
        if (!$request->hasFile('image')) {
            return back()->withErrors(['image' => 'Aucune image n\'a été envoyée.']);
        }

        $file = $request->file('image');
        if (!$file->isValid()) {
            return back()->withErrors(['image' => 'L\'image n\'est pas valide.']);
        }

        try {
            // 3. Création du dossier si nécessaire
            if (!Storage::disk('public')->exists('posts')) {
                Storage::disk('public')->makeDirectory('posts');
            }

            // 4. Stockage de l'image avec un nom unique
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;

            // 5. Stockage direct avec le path complet
            $file->storeAs('public/posts', $fileName);
            $data['image'] = 'posts/' . $fileName;

            // 6. Création du post
            $post = auth()->user()->posts()->create([
                'image' => $data['image'],
                'caption' => $data['caption']
            ]);

            // 7. Redirection en cas de succès
            return redirect()
                ->route('profile.show', auth()->user())
                ->with('status', 'Post créé avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
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
