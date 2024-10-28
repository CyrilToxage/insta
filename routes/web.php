<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;

// Page d'accueil pour les invités
Route::get('/', function () {
    return view('welcome');
});

// Routes protégées par l'authentification
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirection de /dashboard vers la page d'accueil de l'app
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    // Page d'accueil de l'application (feed)
    Route::get('/home', [PostController::class, 'index'])->name('home');

    // Routes pour les posts
    Route::controller(PostController::class)->group(function () {
        Route::get('/posts/create', 'create')->name('posts.create');
        Route::post('/posts', 'store')->name('posts.store');
        Route::get('/posts/{post}', 'show')->name('posts.show');
        Route::delete('/posts/{post}', 'destroy')->name('posts.destroy');
        Route::post('/posts/{post}/like', 'like')->name('posts.like');
        Route::post('/posts/{post}/unlike', 'unlike')->name('posts.unlike');
    });

    // Routes pour les commentaires
    Route::controller(CommentController::class)->group(function () {
        Route::post('/posts/{post}/comments', 'store')->name('comments.store');
        Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy');
    });

    // Routes pour les profils
    Route::controller(ProfileController::class)->group(function () {
        // Routes existantes
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');

        // Nouvelles routes
        Route::get('/profile/{user}', 'show')->name('profile.show');
        Route::post('/profile/{user}/follow', 'follow')->name('profile.follow');
        Route::post('/profile/{user}/unfollow', 'unfollow')->name('profile.unfollow');
    });

    // Route pour la recherche
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::post('/search', [SearchController::class, 'search'])->name('search.post');
});

// Routes d'authentification (Breeze)
require __DIR__.'/auth.php';
