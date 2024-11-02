<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Écoute l'événement après que la base de données est réinitialisée
        Event::listen(DatabaseRefreshed::class, function () {
            // Nettoie le dossier des posts
            $postsPath = public_path('images/posts');
            if (File::exists($postsPath)) {
                File::cleanDirectory($postsPath);
            }

            // Nettoie le dossier des photos de profil
            $profilePath = public_path('images/profile');
            if (File::exists($profilePath)) {
                File::cleanDirectory($profilePath);
            }
        });
    }
}
