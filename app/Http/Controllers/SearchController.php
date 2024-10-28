<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(5)
            ->get();

        $posts = Post::where('caption', 'like', "%{$query}%")
            ->with('user')
            ->take(5)
            ->get();

        return view('search.index', compact('users', 'posts', 'query'));
    }
}
