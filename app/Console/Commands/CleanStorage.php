<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanStorage extends Command
{
    protected $signature = 'storage:clean';
    protected $description = 'Clean post and profile images folders';

    public function handle()
    {
        try {
            // Nettoyer dossier posts
            $postsPath = public_path('images/posts');
            if (File::exists($postsPath)) {
                File::cleanDirectory($postsPath);
            } else {
                File::makeDirectory($postsPath, 0755, true, true);
            }

            // Nettoyer dossier profile
            $profilePath = public_path('images/profile');
            if (File::exists($profilePath)) {
                File::cleanDirectory($profilePath);
            } else {
                File::makeDirectory($profilePath, 0755, true, true);
            }

            $this->info('✓ Les dossiers ont été nettoyés avec succès.');
        } catch (\Exception $e) {
            $this->error('Erreur lors du nettoyage: ' . $e->getMessage());
        }
    }
}
