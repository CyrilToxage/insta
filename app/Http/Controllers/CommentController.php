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

    $comment->load('user');

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    return back();
}
}
