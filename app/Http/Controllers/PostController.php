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

        $posts = Post::whereIn('user_id', $following_ids)
            ->orWhereHas('likes', '>', 0)
            ->with(['user', 'likes'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:2048',
            'caption' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('image')->store('posts', 'public');

        auth()->user()->posts()->create([
            'image' => $path,
            'caption' => $validated['caption'],
        ]);

        return redirect()->route('posts.index');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function like(Post $post)
    {
        auth()->user()->likes()->toggle($post->id);
        return back();
    }
}
