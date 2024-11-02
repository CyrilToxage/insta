<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
{
    $data = $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    $comment = $post->comments()->create([
        'user_id' => auth()->id(),
        'content' => $data['content']
    ]);

    // Charger la relation user pour le JSON
    $comment->load('user');

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'username' => $comment->user->username
                ]
            ]
        ]);
    }

    return back();
}
}
